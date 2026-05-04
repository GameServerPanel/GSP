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

define('LANG_no_games_to_monitor', "Actualmente no hay servidores de juegos instalados.");
define('LANG_status', "Estado");
define('LANG_fail_no_mods', "No hay ningún mod para este juego. Habla con el administrador del panel para que añada mod(s) para este juego");
define('LANG_no_game_homes_assigned', "No tienes ningún servidor asignado a tu cuenta.");
define('LANG_select_game_home_to_configure', "Seleccione el juego que desee configurar");
define('LANG_file_manager', "Editar texto");
define('LANG_configure_mods', "Configurar mods");
define('LANG_install_update_steam', "Instalar/Actualizar via Steam");
define('LANG_install_update_manual', "Instalar/Actualizar manualmente");
define('LANG_assign_game_homes', "Asignar home");
define('LANG_user', "Usuario");
define('LANG_group', "Grupo");
define('LANG_start', "Iniciar");
define('LANG_ogp_agent_ip', "IP del Agente OGP");
define('LANG_max_players', "Jugadores máximos");
define('LANG_max', "Máximo");
define('LANG_ip_and_port', "IP y Puerto");
define('LANG_available_maps', "Mapas disponibles");
define('LANG_map_path', "Ruta de mapas");
define('LANG_available_parameters', "Parámetros disponibles");
define('LANG_start_server', "Iniciar servidor");
define('LANG_start_wait_note', "El encendido del servidor puede tomar un tiempo. Por favor, espere sin cerrar el navegador.");
define('LANG_game_type', "Tipo de juego");
define('LANG_map', "Mapa");
define('LANG_starting_server', "Iniciando el servidor, por favor espere...");
define('LANG_starting_server_settings', "Iniciando con los siguientes parámetros");
define('LANG_startup_params', "Parámetros de arranque");
define('LANG_startup_cpu', "CPU en la que corre el servidor");
define('LANG_startup_nice', "Valor de NICE");
define('LANG_game_home', "Camino de inicio");
define('LANG_server_started', "Servidor iniciado correctamente.");
define('LANG_no_parameter_access', "No tienes acceso a parámetros");
define('LANG_extra_parameters', "Parámetros Extra");
define('LANG_no_extra_param_access', "No tiene acceso a parámetros extra.");
define('LANG_extra_parameters_info', "Estos parámetros se incluirán al final de la línea de comandos de arranque del servidor.");
define('LANG_game_exec_not_found', "El ejecutable %s no se pudo encontrar en el servidor remoto.");
define('LANG_select_params_and_start', "Selecciona los parámetros de inicio y presiona '%s'.");
define('LANG_no_ip_port_pairs_assigned', "No hay ningun puerto para la ip asignada al servidor. Contacte con el administrador.");
define('LANG_unable_to_get_log', "Imposible capturar los logs, reintento en %s.");
define('LANG_server_binary_not_executable', "El archivo binario no es ejecutable.");
define('LANG_server_not_running_log_found', "No se encontro el log.");
define('LANG_ip_port_pair_not_owned', "El par IP:PUERTO no es de su propiedad.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Valor maxplayers inadecuado, el número máximo alcanzable de slots se ha establecido.");
define('LANG_server_running_not_responding', "El servidor esta en marcha, pero no responde,<br>podría haber algún tipo de problema y quizá usted quiera ");
define('LANG_update_started', "Actualización iniciada, espere...");
define('LANG_failed_to_start_steam_update', "Fallo la actualización de Steam. Compruebe los logs.");
define('LANG_failed_to_start_rsync_update', "Fallo la actualización de Rsync. Compruebe los logs.");
define('LANG_update_completed', "Actualización completa.");
define('LANG_update_in_progress', "Actualización en progreso, espere...");
define('LANG_refresh_steam_status', "Refrescar estado de Steam");
define('LANG_refresh_rsync_status', "Actualizar el estado de Rsync");
define('LANG_server_running_cant_update', "No se puede actualizar el juego mientras esté en marcha.");
define('LANG_xml_steam_error', "El juego seleccionado no soporta instalación/actualización de Steam");
define('LANG_mod_key_not_found_from_xml', "No se encontro mod_key en el XML");
define('LANG_stop_update', "Parar actualización");
define('LANG_statistics', "Estadisticas");
define('LANG_servers', "Servidores");
define('LANG_players', "Jugadores");
define('LANG_current_map', "Mapa actual");
define('LANG_stop_server', "Parar servidor");
define('LANG_server_ip_port', "IP:Puerto");
define('LANG_server_name', "Nombre del servidor");
define('LANG_server_id', "Server ID");
define('LANG_player_name', "Nombre del jugador");
define('LANG_score', "Puntuacion");
define('LANG_time', "Tiempo");
define('LANG_no_rights_to_stop_server', "No tiene permisos para parar el servidor.");
define('LANG_no_ogp_lgsl_support', "Este servidor (Corriendo: %s) no tiene soporte LGSL  en OGP y sus estadisticas no se pueden mostrar.");
define('LANG_server_status', "Estado del Servidor");
define('LANG_server_stopped', "El servidor '%s' fue detenido.");
define('LANG_if_want_to_start_homes', "Sí desea iniciar el servidor vaya a %s.");
define('LANG_view_log', "Visor de registro");
define('LANG_if_want_manage', "Sí desea administrar sus juegos lo puede hacer en el");
define('LANG_columns', "columnas");
define('LANG_group_users', "Grupo:");
define('LANG_assigned_to', "Asignado a:");
define('LANG_restart_server', "Reiniciar Servidor");
define('LANG_restarting_server', "Reiniciando el servidor, por favor espere...");
define('LANG_server_restarted', "El servidor '%s' fue reiniciado.");
define('LANG_server_not_running', "El servidor no está en marcha.");
define('LANG_address', "Dirección");
define('LANG_owner', "Propietario");
define('LANG_operations', "Operaciones");
define('LANG_search', "Búsqueda");
define('LANG_maps_read_from', "Mapas leidos desde ");
define('LANG_file', "el archivo");
define('LANG_folder', "Carpeta");
define('LANG_unable_retrieve_mod_info', "Imposible recuperar la informacion sobre el mod.");
define('LANG_unexpected_result_libremote', "Respuesta inesperada del servidor remoto.");
define('LANG_unable_get_info', "Imposible recuperar la informacion");
define('LANG_server_already_running', "El servidor está en marcha.");
define('LANG_already_running_stop_server', "Parar el servidor.");
define('LANG_error_server_already_running', "Error, el servidor está en marcha.");
define('LANG_failed_start_server_code', "Fallo al iniciar el servidor remoto. Código de error: %s");
define('LANG_disabled', "Deshabilitado");
define('LANG_not_found_server', "Servidor remoto no encontrado");
define('LANG_rcon_command_title', "Comandos RCON");
define('LANG_has_sent_to', "se envió a");
define('LANG_need_set_remote_pass', "Necesita configurar la contraseña RCON");
define('LANG_before_sending_rcon_com', "antes de enviar comandos RCON al servidor");
define('LANG_retry', "Reintentar");
define('LANG_page', "Página");
define('LANG_server_cant_start', "El servidor no pudo iniciarse.");
define('LANG_server_cant_stop', "El servidor no pudo detenerse.");
define('LANG_error_occured_remote_host', "Ocurrio un error en el servidor remoto.");
define('LANG_follow_server_status', "Puedes seguir el estado del servidor desde");
define('LANG_addons', "Añadidos");
define('LANG_hostname', "Nombre");
define('LANG_rsync_install', "[Instalación Rsync]");
define('LANG_ping', "Ping");
define('LANG_team', "Equipo");
define('LANG_deaths', "Muertes");
define('LANG_pid', "PID");
define('LANG_skill', "Habilidad");
define('LANG_AIBot', "Bot IA");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Jugador");
define('LANG_port', "Puerto");
define('LANG_rcon_presets', "RCON preestablecidas");
define('LANG_update_from_local_master_server', "Actualización desde el servidor maestro local");
define('LANG_update_from_selected_rsync_server', "Actualizar desde el servidor Rsync seleccionado");
define('LANG_execute_selected_server_operations', "Ejecutar operaciones del servidor seleccionadas");
define('LANG_execute_operations', "Ejecutar operaciones");
define('LANG_account_expiration', "Caducidad de su cuenta");
define('LANG_mysql_databases', "Bases de Datos MySQL");
define('LANG_failed_querying_server', "* Falló la petición al servidor.");
define('LANG_query_protocol_not_supported', "* No hay ningún protocolo de peticiones en OGP que pueda soportar este servidor.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Peticiones desactivadas por opción: Desactivar peticiones a los servidores si son mas de: %s, desde que tiene %s servidores.<br>");
define('LANG_presets_for_game_and_mod', "Comandos RCON para %s con mod %s");
define('LANG_name', "Nombre");
define('LANG_command', "Comando&nbsp;RCON");
define('LANG_add_preset', "Añadir comando");
define('LANG_edit_presets', "Editar comandos preestablecidos");
define('LANG_del_preset', "Borrar");
define('LANG_change_preset', "Cambiar");
define('LANG_send_command', "Enviar comando");
define('LANG_starting_copy_with_master_server_named', "Iniciando la copia de archivos desde el servidor maestro llamado '%s'...");
define('LANG_starting_sync_with', "Iniciando sincronización de archivos con %s...");
define('LANG_refresh_interval', "Intervalo de refresco");
define('LANG_finished_manual_update', "Actualización manual terminada.");
define('LANG_failed_to_start_file_download', "Falló la descarga.");
define('LANG_game_name', "Nombre del juego");
define('LANG_dest_dir', "Directorio de destino");
define('LANG_remote_server', "Servidor Remoto");
define('LANG_file_url', "URL del archivo");
define('LANG_file_url_info', "La direccion URL del archivo a descargar y descomprimir en el directorio.");
define('LANG_dest_filename', "Nombre del archivo de destino");
define('LANG_dest_filename_info', "Nombre completo con el que se va a guardar el archivo.");
define('LANG_update_server', "Actualizar servidor");
define('LANG_unavailable', "No disponible");
define('LANG_upload_map_image', "Subir imagen del mapa");
define('LANG_upload_image', "Subir imagen");
define('LANG_jpg_gif_png_less_than_1mb', "La imagen tiene que ser jpg, gif o png y menor de 1 MB.");
define('LANG_check_dev_console', "Error subiendo el archivo, comprueba la consola de desarrollador del navegador.");
define('LANG_uploaded_successfully', "Subido correctamente.");
define('LANG_cant_create_folder', "No se puede crear la carpeta:<br><b>%s</b>");
define('LANG_cant_write_file', "No se puede escribir el archivo:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Excede la directiva PHP.<br><b>%s</b>.");
define('LANG_unknown_errors', "Error desconocido.");
define('LANG_directory', "Directorio");
define('LANG_view_player_commands', "Ver comandos para jugadores");
define('LANG_hide_player_commands', "Ocultar comandos para jugadores");
define('LANG_no_online_players', "No hay jugadores en linea.");
define('LANG_invalid_game_mod_id', "ID de Juego/Mod inválido.");
define('LANG_auto_update_title_popup', "Steam Auto Update Link");
define('LANG_auto_update_popup_html', "<p>Usa el link de abajo para comprobar y actualizar automáticamente tu servidor vía Steam si es necesario.&nbsp; Puedes hacerlo usando un cronjob o iniciando el proceso manualmente.</p>");
define('LANG_api_links_popup_html', "Seleccione una acción que le gustaría realizar utilizando la API de OGP para este servidor de juegos.&nbsp; Luego, use el siguiente enlace para realizar la acción deseada.&nbsp; Puede ejecutar la acción deseada utilizando un cronjob o haciendo una solicitud directa a él.");
define('LANG_auto_update_copy_me', "Copiar");
define('LANG_auto_update_copy_me_success', "Copiado!");
define('LANG_auto_update_copy_me_fail', "Error al copiar. Por favor, copie manualmente el enlace.");
define('LANG_get_steam_autoupdate_api_link', "Enlace Actualización Automatica");
define('LANG_show_api_actions', "Mostrar acciones API");
define('LANG_api_links', "Enlaces API");
define('LANG_update_attempt_from_nonmaster_server', "El usuario %s intentó actualizar home_id %d desde un servidor no maestro. (ID Home: %d)");
define('LANG_attempting_nonmaster_update', "Está intentando actualizar este servidor desde un servidor no maestro.");
define('LANG_cannot_update_from_own_self', "La actualización del servidor local puede no ejecutarse en un servidor maestro.");
define('LANG_show_server_id', "Mostrar ID de servidor");
define('LANG_hide_server_id', "Ocultar ID de servidor");
define('LANG_edit_configuration_files', "Editar archivos de configuración");
define('LANG_admin', "Admin");
define('LANG_cid', "CID");
define('LANG_phan', "Phantom");
define('LANG_sec', "Segundos");
define('LANG_unknown_rsync_mirror', "Usted intento iniciar una actualización desde un servidor que ya no existe.");
define('LANG_custom_fields', "Campos personalizados");
?>
