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

define('LANG_error', "Error");
define('LANG_title', "Interfaz Web del TeamSpeak 3");
define('LANG_update_available', "<h3>Atención: Una nueva version (v%1) de este software esta disponible en <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Salir");
define('LANG_head_vserver_switch', "Cambiar");
define('LANG_head_vserver_overview', "Configurar");
define('LANG_head_vserver_token', "Gestiónar Token");
define('LANG_head_vserver_liveview', "Monitorizar");
define('LANG_e_fill_out', "Rellene los campos.");
define('LANG_e_upload_failed', "Fallo al subir el archivo.");
define('LANG_e_server_responded', "El servidor a respondido: ");
define('LANG_e_conn_serverquery', "No se pudo obtener acceso al servicio de peticiones.");
define('LANG_e_conn_vserver', "No se puede elegir el servido virtual.");
define('LANG_e_session_timedout', "Sesión caducada.");
define('LANG_js_error', "Error");
define('LANG_js_ajax_error', "Se ha producido un error AJAX: %1.");
define('LANG_js_confirm_server_stop', "Seguro que quieres parar el servidor #%1?");
define('LANG_js_confirm_server_delete', "Seguro que quiere eliminar el servidor #%1?");
define('LANG_js_notice_server_deleted', "El servidor %1 ha sido eliminado.");
define('LANG_js_prompt_banduration', "Duración en horas (0=ilimitado): ");
define('LANG_js_prompt_banreason', "Razón (opcional): ");
define('LANG_js_prompt_msg_to', "Mensaje de texto a %1 #%2: ");
define('LANG_js_prompt_poke_to', "Mensaje de Poke al cliente #%1: ");
define('LANG_js_prompt_new_propvalue', "Nuevo valor para '%1': ");
define('LANG_n_server_responded', "El servidor a respondido: ");
define('LANG_login_serverquery', "Ususario del Servidor de peticiones");
define('LANG_login_name', "Nombre de usuario");
define('LANG_login_password', "contraseña");
define('LANG_login_submit', "Entrar");
define('LANG_vsselect_headline', "Selección de servidor");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Nombre");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Puerto");
define('LANG_vsselect_state', "Estado");
define('LANG_vsselect_clients', "Clientes");
define('LANG_vsselect_uptime', "Tiempo en linea");
define('LANG_vsselect_choose', "Seleccionar");
define('LANG_vsselect_start', "Iniciar");
define('LANG_vsselect_stop', "Parar");
define('LANG_vsselect_delete', "ELIMINAR");
define('LANG_vsselect_new_headline', "Crear un nuevo servidor virtual");
define('LANG_vsselect_new_servername', "Nombre del servidor");
define('LANG_vsselect_new_slots', "Slots de clientes");
define('LANG_vsselect_new_create', "Crear");
define('LANG_vsselect_new_added_ok', "servidor <span class=\"online\">%1</span> se ha creado.");
define('LANG_vsselect_new_added_generated', "El token generado es:");
define('LANG_vsoverview_virtualserver', "Servidor Virtual");
define('LANG_vsoverview_information_head', "Información");
define('LANG_vsoverview_connection_head', "Conexión");
define('LANG_vsoverview_info_general_head', "Configuración General");
define('LANG_vsoverview_info_servername', "Nombre de servidor");
define('LANG_vsoverview_info_host', "Host");
define('LANG_vsoverview_info_state', "Estado");
define('LANG_vsoverview_info_state_port', "Puerto");
define('LANG_vsoverview_info_uptime', "Tiempo en linea");
define('LANG_vsoverview_info_welcomemsg', "Mensaje<br />Bienvenida");
define('LANG_vsoverview_info_hostmsg', "Mensaje del Host");
define('LANG_vsoverview_info_hostmsg_mode_output', "Consola");
define('LANG_vsoverview_info_hostmsg_mode_0', "Ninguna");
define('LANG_vsoverview_info_hostmsg_mode_1', "en el log del chat");
define('LANG_vsoverview_info_hostmsg_mode_2', "Ventana");
define('LANG_vsoverview_info_hostmsg_mode_3', "Ventana + Desconectar");
define('LANG_vsoverview_info_req_security', "Nivel de seguridad");
define('LANG_vsoverview_info_req_securitylvl', "Requerido");
define('LANG_vsoverview_info_hostbanner_head', "Cabecera Host");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "URL de imagen");
define('LANG_vsoverview_info_hostbanner_buttonurl', "URL boton del host");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Aviso Activado");
define('LANG_vsoverview_info_antiflood_kick', "Kick Activado");
define('LANG_vsoverview_info_antiflood_ban', "Ban Activado");
define('LANG_vsoverview_info_antiflood_banduration', "Duración Ban");
define('LANG_vsoverview_info_antiflood_decrease', "Disminución");
define('LANG_vsoverview_info_antiflood_points', "puntos");
define('LANG_vsoverview_info_antiflood_in_seconds', "segundos");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Puntos por tick");
define('LANG_vsoverview_conn_total_head', "Total");
define('LANG_vsoverview_conn_total_packets', "paquetes");
define('LANG_vsoverview_conn_total_bytes', "bytes");
define('LANG_vsoverview_conn_total_send', "enviados");
define('LANG_vsoverview_conn_total_received', "recividos");
define('LANG_vsoverview_conn_bandwidth_head', "Ancho de banda");
define('LANG_vsoverview_conn_bandwidth_last', "último");
define('LANG_vsoverview_conn_bandwidth_second', "segundo");
define('LANG_vsoverview_conn_bandwidth_minute', "minuto");
define('LANG_vsoverview_conn_bandwidth_send', "enviados");
define('LANG_vsoverview_conn_bandwidth_received', "recividos");
define('LANG_vstoken_token_virtualserver', "Servidor Virtual");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Tipo de grupo");
define('LANG_vstoken_token_id1', "Grupo del servidor/<br />Canal del grupo");
define('LANG_vstoken_token_id2', "(Canal)");
define('LANG_vstoken_token_tokencode', "Código Token");
define('LANG_vstoken_token_delete', "Borrar");
define('LANG_vstoken_new_head', "Crear nuevo token");
define('LANG_vstoken_new_create', "Generar");
define('LANG_vstoken_new_tokentype', "Tipo de token:");
define('LANG_vstoken_new_servergroup', "Grupo del servidor");
define('LANG_vstoken_new_channelgroup', "Grupo del Canal");
define('LANG_vstoken_new_select_group', "Selección de grupo");
define('LANG_vstoken_new_select_channelgroup', "Selección del canal de grupo");
define('LANG_vstoken_new_select_channel', "Canal");
define('LANG_vstoken_new_tokentype_0', "Servidor");
define('LANG_vstoken_new_tokentype_1', "Canal");
define('LANG_vstoken_new_added_ok', "Token generado.");
define('LANG_vsliveview_server_virtualserver', "Servidor virtual");
define('LANG_vsliveview_server_head', "Vista en Vivo");
define('LANG_vsliveview_liveview_enable_autorefresh', "Autorefrescar");
define('LANG_vsliveview_liveview_tooltip_to_channel', "al canal #");
define('LANG_vsliveview_liveview_tooltip_switch', "Mover");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Enviar mensaje");
define('LANG_vsliveview_liveview_tooltip_poke', "Meter");
define('LANG_vsliveview_liveview_tooltip_kick', "Expulsar");
define('LANG_vsliveview_liveview_tooltip_ban', "Ban");
define('LANG_vsoverview_banlist_head', "Lista de Baneados");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Nombre");
define('LANG_vsoverview_banlist_uid', "ID único");
define('LANG_vsoverview_banlist_reason', "Razon");
define('LANG_vsoverview_banlist_created', "Creado");
define('LANG_vsoverview_banlist_duration', "Duración");
define('LANG_vsoverview_banlist_end', "Termina");
define('LANG_vsoverview_banlist_unlimited', "ilimitado");
define('LANG_vsoverview_banlist_never', "Nunca");
define('LANG_vsoverview_banlist_new_head', "Crear ban");
define('LANG_vsoverview_banlist_new_create', "Crear");
define('LANG_vsliveview_channelbackup_head', "Respaldar Canal");
define('LANG_vsliveview_channelbackup_get', "Crear y Descargar");
define('LANG_vsliveview_channelbackup_load', "Subir Respaldo del Canal");
define('LANG_vsliveview_channelbackup_load_submit', "Recrear");
define('LANG_vsliveview_channelbackup_new_added_ok', "Respaldo del canal realizado correctamente.");
define('LANG_time_day', "dia");
define('LANG_time_days', "dias");
define('LANG_time_hour', "hora");
define('LANG_time_hours', "horas");
define('LANG_time_minute', "minuto");
define('LANG_time_minutes', "minutos");
define('LANG_time_second', "segundo");
define('LANG_time_seconds', "segundos");
define('LANG_e_2568', "No tienes permisos suficientes.");
define('LANG_temp_folder_not_writable', "La descarga no pudo completarse porque Apache no tiene permisos de escritura en la carpeta de archivos temporales del sistema(%s).");
define('LANG_unassign_from_subuser', "Desasignar del subusuario.");
define('LANG_assign_to_subuser', "Asignar al subusuario.");
define('LANG_select_subuser', "Selecciona subusuario.");
define('LANG_no_ts3_servers_assigned_to_account', "No tienes servidores asignados a tu cuenta.");
define('LANG_change_virtual_server', "Cambiar servidor virtual");
define('LANG_change_remote_server', "Cambiar servidor remoto");
?>