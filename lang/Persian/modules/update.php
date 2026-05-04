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

include('litefm.php');
define('LANG_curl_needed', "این صفحه نیاز به ماژول PHP cURL دارد.");
define('LANG_no_access', "حساب کاربری شما باید \"مدیر\" باشد تا بتوانید به این صفحه دسترسی داشته باشید.");
define('LANG_dwl_update', "بروزرسانی درحال دانلود است...");
define('LANG_dwl_complete', "دانلود به پایان رسید");
define('LANG_install_update', "بروزرسانی در حال نصب است...");
define('LANG_update_complete', "بروزرسانی به پایان رسید");
define('LANG_ignored_files', "%s ignored file(s)");
define('LANG_not_updated_files_blacklisted', "Not updated/installed files (blacklisted):<br>%s");
define('LANG_latest_version', "نسخه آخر");
define('LANG_panel_version', "نسخه پنل");
define('LANG_update_now', "بروزرسانی");
define('LANG_the_panel_is_up_to_date', "پنل به روز است.");
define('LANG_files_overwritten', "%s فایل Overwrite شدند.");
define('LANG_files_not_overwritten', "%s files are NOT overwritten due to blacklist");
define('LANG_can_not_update_non_writable_files', "امکان به روزرسانی وجود ندارد، فایل ها یا دایرکتوری های مورد نظر Writable نیستند.");
define('LANG_dwl_failed', "لینک دانلود درحال حاضر در دسترس نیست: \"%s\".<br> بعدا سعی کنید.");
define('LANG_temp_folder_not_writable', "The download can not be placed, because Apache does not have write permission at the system temporary folder (%s).");
define('LANG_base_dir_not_writable', "پنل نمیتواند به روزرسانی شود، Apache دست رسی Write کردن بر روی \"%s\" را ندارد.");
define('LANG_new_files', "%s فایل جدید.");
define('LANG_updated_files', "فایل های به روز شده:<br>%s");
define('LANG_select_mirror', "انتخاب میرور");
define('LANG_view_changes', "مشاهده تغییرا");
define('LANG_updating_modules', "به روزرسانی ماژول ها");
define('LANG_updating_finished', "به روزرسانی به پایان رسید");
define('LANG_updated_module', "ماژول \"%s\" به روزرسانی شد.");
define('LANG_blacklist_files', "فایل های لیست سیاه");
define('LANG_blacklist_files_info', "تمام فایل های نشان شده، به روزرسانی نمی شوند.");
define('LANG_save_to_blacklist', "ذخیره در لیست سیاه");
define('LANG_no_new_updates', "به روزرسانی جدیدی موجود نیست");
define('LANG_module_file_missing', "دایرکتوری فایل module.php را پیدا نمی کند.");
define('LANG_query_failed', "اجرای Query با شکست مواجه شد");
define('LANG_query_failed_2', "به پایگاه داده.");
define('LANG_missing_zip_extension', "The php-zip extension is not loaded. Please, enable it to use the Update module.");
?>