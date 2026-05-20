<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

define('LANG_module_name', "Utilidades");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Traceroute");
define('LANG_network_tools', "Herramientas de Red");
define('LANG_sourcemod_admins', "Administradores Sourcemod");
define('LANG_steam_converter', "Convertidor SteamID");
define('LANG_your_ip', "Tu dirección IP:");
define('LANG_loading_agents', "Cargando agentes en línea ...");
define('LANG_loading_failed', "Error al cargar agentes.");
define('LANG_agents_offline', "Todos los agentes están fuera de línea.");
define('LANG_no_commands', "Perdón, tu cuenta de usuario no tiene comandos disponibles.");
define('LANG_remote_target', "Dirección IP de destino:");
define('LANG_command', "Comando:");
define('LANG_select_agent', "Seleccionar Agente:");
define('LANG_chdir_failed', "Error: chdir() retorno falso.");
define('LANG_agent_invalid', "Se ha especificado el Agente no válido.");
define('LANG_networktools_agent_offline', "No se puede ejecutar el comando en el Agente seleccionado, porque está sin conexión.");
define('LANG_target_empty', "No se ha asignado dirección de destino.");
define('LANG_command_empty', "No se ha seleccionado ningún comando.");
define('LANG_command_unavilable', "El comando seleccionado no está disponible en el agente seleccionado.");
define('LANG_target_invalid', "Introdujiste una Ip invalida/Nombre de host.");
define('LANG_exec_failed', "Tiempo de respuesta agotado.");
define('LANG_command_no_access', "No tienes acceso a este comando. Este incidente se registrara.");
define('LANG_command_hacking_attempt', "Se han introducido caracteres de la lista negra. Este incidente se registrara.");
define('LANG_command_bad_characters', "Se ha intentado introducir comandos con caracteres maliciosos. Entrada recibida %s %s");
define('LANG_command_no_permissions', "Se ha intentado ejecutar un comando con permisos insuficientes. Entrada recibida: %s %s");
define('LANG_command_executed', "Envió correctamente el siguiente comando: %s %s");
define('LANG_no_servers', "No tiene ningún servidor asignado a su cuenta.");
define('LANG_select_server', "Seleccionar servidor:");
define('LANG_select_server_option', "Seleccionar...");
define('LANG_steamid', "ID Steam:");
define('LANG_immunity', "Inmunidad:");
define('LANG_sourcemod_perms', "Permisos Sourcemod:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('LANG_sourcemod_perm_custom', "Sourcemod Flags personalizadas");
define('LANG_sourcemod_flag_a', "Slot reservado.");
define('LANG_sourcemod_flag_b', "Admin genérico; requerido para admins.");
define('LANG_sourcemod_flag_c', "Expulsar otros jugadores.");
define('LANG_sourcemod_flag_d', "Banear otros jugadores.");
define('LANG_sourcemod_flag_e', "Remover bans.");
define('LANG_sourcemod_flag_f', "Matar/dañar a otro jugadores.");
define('LANG_sourcemod_flag_g', "Cambiar de mapa o las principales características de juego.");
define('LANG_sourcemod_flag_h', "Modificar la mayoría de las CVARs.");
define('LANG_sourcemod_flag_i', "Ejecutar archivos de configuración");
define('LANG_sourcemod_flag_j', "Privilegios especiales de chat");
define('LANG_sourcemod_flag_k', "Iniciar o crear votaciones");
define('LANG_sourcemod_flag_l', "Introducir contraseña al servidor.");
define('LANG_sourcemod_flag_m', "Usar comandos de RCON");
define('LANG_sourcemod_flag_n', "Modificar sv_cheats o usar comandos de cheats");
define('LANG_sourcemod_flag_o', "Grupo personalizado 1.");
define('LANG_sourcemod_flag_p', "Grupo personalizado 2.");
define('LANG_sourcemod_flag_q', "Grupo personalizado 3.");
define('LANG_sourcemod_flag_r', "Grupo personalizado 4.");
define('LANG_sourcemod_flag_s', "Grupo personalizado 5.");
define('LANG_sourcemod_flag_t', "Grupo personalizado 6.");
define('LANG_rcon_reload_admins_failed', "Fallo al recargar caché de admins vía RCON; está en línea?");
define('LANG_reload_admins_failed', "No se pudo volver a cargar el caché de administración; \"sm_reloadadmins\" es un comando desconocido.");
define('LANG_reload_admins_success', "Se agrego con exito %s a admins_simple.ini y se volvió a cargar el cache de administración.");
define('LANG_add_success_no_rcon', "Se agrego con éxito %s a admins_simple.ini, pero no se pudo volver a cargar el cache de administrador.");
define('LANG_writefile_error', "Se ha producido un error desconocido de escritura en: %s");
define('LANG_remotefile_nonexistent', "No se puede agregar un nuevo administrador. El archivo de administrador: %s no existe en este servidor.");
define('LANG_empty_flag_list', "No has seleccionado ningún acceso de administrador.");
define('LANG_invalid_steam_format', "El SteamID que ingreso no coincido con el patrón requerido.");
define('LANG_selected_server_offline', "No se puede agregar un administrador, el agente que controla el servidor seleccionado no está conectado.");
define('LANG_malformed_form', "Has enviado un formulario con elementos ocultos mal formados - no puedes agregar un administrador.");
define('LANG_empty_form_data', "Por favor llene todos los elementos del formulario.");
define('LANG_server_not_selected', "No ha seleccionado un servidor.");
define('LANG_invalid_steamid', "Ha introducido un Steam ID no válido.");
define('LANG_invalid_immunity', "Ha introducido un valor de inmunidad no válido.");
define('LANG_submit', "Enviar");
define('LANG_post_failed', "La acción POST falló. No se puede recuperar una respuesta.");
define('LANG_amx_mod_admins', "Admins AMX mod X");
define('LANG_amx_login_type', "Tipo de identificación");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Apodo + Contraseña");
define('LANG_nickname', "Apodo");
define('LANG_amx_mod_perms', "Permisos AMX mod X:");
define('LANG_amx_mod_perm_root', "AMX mod C todos los Flags");
define('LANG_amx_mod_perm_custom', "AMX mod X Flags personalizados.");
define('LANG_amx_mod_flag_a', "Inmunidad (no puede ser kickeado/baneado/slayeado/slapeado o afectado por otros comandos)");
define('LANG_amx_mod_flag_b', "Reservados (puede usar slots reservados)");
define('LANG_amx_mod_flag_c', "Comando amx_kick");
define('LANG_amx_mod_flag_d', "Comandos amx_ban y amx_unban");
define('LANG_amx_mod_flag_e', "Comandos amx_slay y amx_slap");
define('LANG_amx_mod_flag_f', "Comando amx_map");
define('LANG_amx_mod_flag_g', "Comando amx_cvar (no todas las cvar estarán disponibles)");
define('LANG_amx_mod_flag_h', "Comando amx_cfg");
define('LANG_amx_mod_flag_i', "amx_chat y otros comandos de chat");
define('LANG_amx_mod_flag_j', "amx_vote y otros comandos de votación");
define('LANG_amx_mod_flag_k', "acceso a cvar sv_password (a través del comando amx_cvar)");
define('LANG_amx_mod_flag_l', "Acceso a comando amx_rcon y cvar rcon_password (a través del comando amx_cvar)");
define('LANG_amx_mod_flag_m', "Nivel personalizado A (para plugins adicionales)");
define('LANG_amx_mod_flag_n', "Nivel personalizado B");
define('LANG_amx_mod_flag_o', "Nivel personalizado C");
define('LANG_amx_mod_flag_p', "Nivel personalizado D");
define('LANG_amx_mod_flag_q', "Nivel personalizado E");
define('LANG_amx_mod_flag_r', "Nivel personalizado F");
define('LANG_amx_mod_flag_s', "Nivel personalizado G");
define('LANG_amx_mod_flag_t', "Nivel personalizado H");
define('LANG_amx_mod_flag_u', "Acceso al menu");
?>