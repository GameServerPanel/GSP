<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

function extractZip( $zipFile, $extract_path, $remove_path = '', $blacklist = '', $whitelist = '' )
{
	$temp_path = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	$base_path = rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

	if (!file_exists($extract_path))
	{
		return "Destination path ({$extract_path}) does not exist.\n";
	}

	if (!is_writable($extract_path))
	{
		return "Can't extract to {$extract_path}, not writable.\n";
	}

	if ($zipFile === '' || $extract_path === '')
		return "Invalid arguments.\n";
	if (!file_exists($zipFile))
		return "Unable to read {$zipFile}.\n";

	$zip = new ZipArchive();
	$res = $zip->open($zipFile);
	if ($res !== true)
	{
		return "{$zipFile} is corrupt or cannot be opened (error code: {$res}).\n";
	}

	$remove_path_escaped = addcslashes($remove_path, "/");

	$i  = 0;
	$i2 = 0;
	$extracted_files = array();
	$ignored_files   = array();

	// Resolve the real extraction root once for Zip Slip checks.
	$real_extract_root = realpath($extract_path);
	if ($real_extract_root === false)
	{
		$zip->close();
		return "Cannot resolve extraction path {$extract_path}.\n";
	}
	$norm_root = str_replace('\\', '/', $real_extract_root);

	for ($idx = 0; $idx < $zip->numFiles; $idx++)
	{
		$filename  = $zip->getNameIndex($idx);
		$file_path = preg_replace("/{$remove_path_escaped}/", '', $filename);
		$dir_path  = preg_replace("/{$remove_path_escaped}/", '', dirname($filename));

		// Zip Slip protection: reject entries with path traversal or absolute paths.
		$norm_filename = str_replace('\\', '/', $filename);
		if (strpos($norm_filename, '../') !== false || substr($norm_filename, 0, 1) === '/')
		{
			continue;
		}

		if (isset($blacklist) && is_array($blacklist) && in_array($file_path, $blacklist))
		{
			if (isset($whitelist) && is_array($whitelist) && in_array($filename, $whitelist))
			{
				$ignored_files[$i2] = $file_path;
				$i2++;
			}
			continue;
		}
		if (isset($whitelist) && is_array($whitelist) && !in_array($filename, $whitelist))
			continue;

		$completePath = $extract_path . $dir_path;
		$completeName = $extract_path . $file_path;
		$escaped_temp_path = str_replace('\\', '\\\\', $temp_path); // For Windows paths backslashes
		$root = preg_match("#^{$escaped_temp_path}#", $completePath) ? $temp_path : $base_path;
		$escaped_root  = str_replace('\\', '\\\\', $root);
		$relative_path = preg_replace("#^{$escaped_root}(.*)$#", '$1', $completePath);

		// Cache this repeated check to avoid duplication.
		$dirname_in_path = preg_match('/^' . $remove_path_escaped . '/', dirname($filename));

		// Walk through path to create non-existing directories.
		// This won't apply to empty directories – they are created further below.
		if (!file_exists($completePath) && $dirname_in_path)
		{
			$tmp = $root;
			foreach (preg_split('/(\/|\\\\)/', $relative_path) as $k)
			{
				if ($k !== '')
				{
					$tmp .= $k . DIRECTORY_SEPARATOR;
					if (!file_exists($tmp))
					{
						if (!mkdir($tmp, 0777))
						{
							$zip->close();
							return "Unable to write folder {$tmp}.\n";
						}
					}
				}
			}
		}

		if ($dirname_in_path)
		{
			if (!preg_match('/\/$/', $completeName))
			{
				// Secondary Zip Slip check: verify the destination path (after normalizing
				// the string, since the file may not exist yet) stays within the extraction
				// root. Because we already blocked '../' in the entry name above, this is
				// belt-and-suspenders for path-manipulation edge-cases.
				$norm_complete = str_replace('\\', '/', $completeName);
				if (strpos($norm_complete . '/', $norm_root . '/') !== 0)
				{
					continue; // Path escapes the extraction root – skip.
				}

				// Stream the entry to disk without loading it entirely into memory.
				$stream = $zip->getStream($filename);
				if ($stream === false)
				{
					$zip->close();
					return "Unable to read entry {$filename} from ZIP.\n";
				}
				$fd = fopen($completeName, 'w+');
				if ($fd === false)
				{
					fclose($stream);
					$zip->close();
					return "Unable to write file {$completeName}.\n";
				}
				stream_copy_to_stream($stream, $fd);
				fclose($stream);
				fclose($fd);
				$extracted_files[$i]['filename'] = $filename;
				$i++;
			}
		}
	}

	$zip->close();
	return array('ignored_files' => $ignored_files, 'extracted_files' => $extracted_files);
}
?>