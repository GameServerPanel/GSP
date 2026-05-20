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

define('LANG_create_alias', "Crear alias y carpeta");
define('LANG_save_as', "Guardar como");
define('LANG_failure', "Error, no se pudo generar el archivo de alias.");
define('LANG_success', "Éxito");
define('LANG_fast_download_service_for', "Servicio de redirección de descargas para %s");
define('LANG_to_the_path', "para la ruta");
define('LANG_at_url', "en la URL");
define('LANG_create_alias_for', "Crear alias para");
define('LANG_fast_dl', "Redirección de descargas (FastDL)");
define('LANG_current_aliases_at_remote_server', "Alias actuales en el servidor remoto");
define('LANG_delete_selected_aliases', "Elimina los alias seleccionados");
define('LANG_no_aliases_defined', "Aún no hay alias web definidos por OGP para este servidor remoto.");
define('LANG_fastdl_port', "Puerto");
define('LANG_fastdl_port_info', "Puerto en el que se iniciará el daemon de descarga rápida.");
define('LANG_fastdl_ip', "Dirección");
define('LANG_fastdl_ip_info', "La IP o dominio en el que tu servidor Fast Download se iniciará, los dominios tienen que estar descritos en /etc/hosts.");
define('LANG_listing', "Listar");
define('LANG_listing_info', "Si está activado el servidor mostrara una lista del contenido de las carpetas.");
define('LANG_fast_dl_advanced', "Configuración Avanzada");
define('LANG_apply_settings_and_restart_fastdl', "Guardar la configuracion del servicio de redirección de descargas y reiniciarlo");
define('LANG_stop_fastdl', "Detener el servicio de redirección de descargas.");
define('LANG_fast_download_daemon_running', "El servicio de redirección de descargas está en marcha.");
define('LANG_fast_download_daemon_not_running', "El servicio de redirección de descargas está parado.");
define('LANG_fastdl_could_not_be_restarted', "No fue posible reiniciar el servicio Fast Download.");
define('LANG_configuration_file_could_not_be_written', "No se pudo escribir en el archivo de configuración.");
define('LANG_remove_folders', "Eliminar carpeta para los alias seleccionados.");
define('LANG_remove_folder', "Eliminar carpeta");
define('LANG_delete_alias', "Eliminar alias");
define('LANG_no_game_homes_assigned', "No tienes ningún servidor asignado a tu cuenta.");
define('LANG_select_remote_server', "Seleccione servidor remoto");
define('LANG_access_rules', "Reglas de acceso");
define('LANG_create_aliases', "Crear alias");
define('LANG_select_game', "Seleccione un juego");
define('LANG_games_without_specified_rules', "Juegos sin reglas especificadas");
define('LANG_match_file_extension', "Coincide la extension");
define('LANG_match_file_extension_info', "Especifique extensiones separadas por coma,<br> los archivos que coincidan serán accesibles.<br><b>Vacio para acceso ilimitado.</b>.");
define('LANG_match_client_ip', "Coincide la IP del cliente");
define('LANG_match_client_ip_info', "Las conexiones con IP coincidente serán concedidas,<br>Vacio para acceso ilimitado.<br>Puede usar multiples IPs<br>o rangos separados por coma:<br>Subredes /xx<br>Ejemplo: 10.0.0.0/16<br>Subredes /xxx.xxx.xxx.xxx<br>Ejemplo: 10.0.0.0/255.0.0.0<br>Rango establecido por un guión<br>Ejemplo: 10.0.0.5-230<br>Coincidencia por asterisco<br>Ejemplo: 10.0.*.*");
define('LANG_save_access_rules', "Guardar reglas");
define('LANG_create_access_rules', "Crear reglas de acceso");
define('LANG_invalid_entries_found', "Se han encontrado entradas invalidas");
define('LANG_game_name', "Nombre del juego");
define('LANG_alias_already_exists', "El alias '%s' ya existe.");
define('LANG_warning_access_rules_applied_once_alias_created', "AVISO: Las reglas de acceso son aplicadas cuando se crea el alias. Ningún cambio será aplicado a los alias existentes.");
define('LANG_autostart_on_agent_startup', "Inicio automático en el inicio del agente");
define('LANG_autostart_on_agent_startup_info', "Inicie el daemon de descarga rápida automáticamente cuando se inicie el Agente.");
define('LANG_port_forwarded_to_80', "Puerto redireccionado desde el puerto 80");
define('LANG_port_forwarded_to_80_info', "Activa esta opción si el puerto 80 ha sido redireccionado al puerto configurado para este servicio, por lo que se ocultará en las URLs.");
define('LANG_current_access_rules', "Reglas de acceso actuales");
?>