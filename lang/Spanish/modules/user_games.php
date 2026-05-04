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

define('LANG_add_mods_note', "Usted necesita agregar mods después de agregar el servidor al usuario.<br>Esto se puede hacer mediante la edición del servidor.");
define('LANG_game_servers', "Servidores de Juegos");
define('LANG_game_path', "Carpeta del juego");
define('LANG_game_path_info', "Una ruta absoluta del servidor. Ejemplo: /home/ogpbot/OGP_User_Files/My_Server");
define('LANG_game_server_name_info', "Ayuda a identificar el servidor.");
define('LANG_control_password', "RCON");
define('LANG_control_password_info', "Esta contraseña se usa para el control del servidor, en algunos casos se utiliza para RCON, o como contraseña de serveradmin en TS3. Si la contraseña esta vacía algunas funciones podrían no estar disponibles.");
define('LANG_add_game_home', "Agregar Home");
define('LANG_game_path_empty', "La direccion de la carpeta del juego no puede dejarse en blanco.");
define('LANG_game_home_added', "Home agregada, redireccionando a edicion de home.");
define('LANG_failed_to_add_home_to_db', "Error al agregar el home a la base de datos. Error: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Peligro!</b> El agente esta inaccesible, no se pude detectar el SO ni la arquitectura,<br> Mostrando los servidores para todas las plataformas:");
define('LANG_select_remote_server', "Seleccione Servidor Remoto:");
define('LANG_no_remote_servers_configured', "No hay servidores remotos configurados");
define('LANG_no_game_configurations_found', "No se encontraron configuraciones XML");
define('LANG_game_configurations', "Configuraciones de juegos");
define('LANG_add_remote_server', "Agregar Servidor remoto");
define('LANG_wine_games', "Juegos Wine");
define('LANG_home_path', "Carpeta Home");
define('LANG_change_home_info', "Ejemplo: /home/ogp_agent/mi_servidor/");
define('LANG_game_server_name', "Nombre del servidor");
define('LANG_change_name_info', "Este nombre ayuda a identificar el servidor.");
define('LANG_game_control_password', "Contraseña de control");
define('LANG_change_control_password_info', "La contraseña de control es por ejemplo rcon password.");
define('LANG_available_mods', "Mods disponibles");
define('LANG_note_no_mods', "Este home no tiene mods asignados.");
define('LANG_change_home', "Cambiar Home");
define('LANG_change_control_password', "Cambiar RCON");
define('LANG_change_name', "Cambiar nombre");
define('LANG_add_mod', "Agregar Mod");
define('LANG_set_ip', "Asignar IP");
define('LANG_ips_and_ports', "IPs y Puertos");
define('LANG_mod_name', "Nombre del Mod");
define('LANG_max_players', "Max Jugadores");
define('LANG_extra_cmd_line_args', "Comandos de lanzamiento extra");
define('LANG_extra_cmd_line_info', "Permite añadir comandos nuevos a la linea de comandos de arranque.");
define('LANG_cpu_affinity', "Afinidad de CPU");
define('LANG_nice_level', "Nivel de Prioridad");
define('LANG_set_options', "Cambiar opciones");
define('LANG_remove_mod', "Borrar");
define('LANG_mods', "Mods");
define('LANG_ip', "IP");
define('LANG_port', "Puerto");
define('LANG_no_ip_ports_assigned', "Se deben asignar una IP y un puerto como minimo.");
define('LANG_successfully_assigned_ip_port', "Asignados IP:Puerto al home.");
define('LANG_port_range_error', "Los puertos deben pertenecer al rango entre 0 y 65535. En Linux, solo root puede usar puertos por debajo de 1024, por lo que el agente no será capaz de iniciar ningún servidor que use un puerto inferior a 1024.");
define('LANG_failed_to_assing_mod_to_home', "No se pudo asignar el mod %d al home.");
define('LANG_successfully_assigned_mod_to_home', "Se asigno el mod con id %d al home.");
define('LANG_successfully_modified_mod', "La informacion del mod ha sido modificada correctamente.");
define('LANG_back_to_game_monitor', "Volver a Monitor");
define('LANG_back_to_game_servers', "Volver a Servirdores de Juegos");
define('LANG_user_id_main', "Propietário principal");
define('LANG_change_user_id_main', "Cambiar propietário principal");
define('LANG_change_user_id_main_info', "Propietario principal del servidor de juegos.");
define('LANG_server_ftp_password', "Contraseña FTP");
define('LANG_change_ftp_password', "Cambiar la contraseña FTP");
define('LANG_change_ftp_password_info', "Esta es la contraseña para entrar al servidor FTP de este servidor de juegos.");
define('LANG_Delete_old_user_assigned_homes', "Quitar el servidor a los usuarios actuales.");
define('LANG_editing_home_called', "Editando el servidor llamado");
define('LANG_control_password_updated_successfully', "password RCON actualizado correctamente.");
define('LANG_control_password_update_failed', "No se pudo cambiar la RCON");
define('LANG_successfully_changed_game_server', "Opciones cambiadas correctamente.");
define('LANG_error_ocurred_on_remote_server', "Ocurrio un error en el servidor remoto.");
define('LANG_ftp_password_can_not_be_changed', "No se pudo cambiar el password FTP.");
define('LANG_ftp_can_not_be_switched_on', "No se pudo activar el FTP.");
define('LANG_ftp_can_not_be_switched_off', "No se pudo desactivar el FTP.");
define('LANG_invalid_home_id_entered', "Introdujo un identificador incorrecto");
define('LANG_ip_port_already_in_use', "El par de IP y puerto esta en uso.");
define('LANG_successfully_assigned_ip_port_to_server_id', "Se asigno el par IP:Puerto %s:%s al servidor ID %s.");
define('LANG_no_ip_addresses_configured', "No hay direcciones IP assignadas.");
define('LANG_server_page', "Pagina del servidor");
define('LANG_successfully_removed_mod', "Mod eliminado.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Peligro, Agente inaccesible, el computo de CPU's se ajusto a 1.");
define('LANG_mod_install_cmds', "Comandos de instalación");
define('LANG_cmds_for', "Comandos para");
define('LANG_preinstall_cmds', "Comandos preinstalación");
define('LANG_postinstall_cmds', "Comandos postinstalación");
define('LANG_edit_preinstall_cmds', "Editar comandos de preinstalación");
define('LANG_edit_postinstall_cmds', "Editar comandos de postinstalación");
define('LANG_save_as_default_for_this_mod', "Guardar por defecto para este mod");
define('LANG_empty', "vacío");
define('LANG_master_server_for_clon_update', "Servidor maestro para actualización local");
define('LANG_set_as_master_server', "Asignar como servidor maestro");
define('LANG_set_as_master_server_for_local_clon_update', "Configurar como servidor maestro para actualización local.");
define('LANG_only_available_for', "Solo estará disponible para servidores de '%s' alojados en el servidor remoto llamado '%s'.");
define('LANG_ftp_on', "Activar FTP");
define('LANG_ftp_off', "Desactivar FTP");
define('LANG_change_ftp_account_status', "Cambiar estado del FTP");
define('LANG_change_ftp_account_status_info', "Una vez que una cuenta FTP está habilitada o deshabilitada, se agrega o se elimina de la base de datos del FTP.");
define('LANG_server_ftp_login', "Usuário FTP");
define('LANG_change_ftp_login_info', "Cambie el nombre de usuário FTP por uno a su elección.");
define('LANG_change_ftp_login', "Cambiar usuário FTP");
define('LANG_ftp_login_can_not_be_changed', "No se pudo cambiar el usuário FTP.");
define('LANG_server_is_running_change_addresses_not_available', "El servidor está en marcha, la IP no puede ser cambiada.");
define('LANG_change_game_type', "Cambiar tipo de juego");
define('LANG_change_game_type_info', "Al cambiar el tipo de juego actual se eliminará la configuración de los mods.");
define('LANG_force_mod_on_this_address', "Forzar mod en esta dirección");
define('LANG_successfully_assigned_mod_to_address', "Asignado mod a la dirección correctamente");
define('LANG_switch_mods', "Cambiar mods");
define('LANG_switch_mod_for_address', "Cambiar mod para la dirección %s");
define('LANG_invalid_path', "Ruta no valida");
define('LANG_add_new_game_home', "Crear nueva Home");
define('LANG_no_game_homes_found', "No se encontraron Homes");
define('LANG_available_game_homes', "Homes disponibles");
define('LANG_home_id', "Home ID");
define('LANG_game_server', "Servidor");
define('LANG_game_type', "Tipo de juego");
define('LANG_game_home', "Camino de inicio");
define('LANG_game_home_name', "Nombre del Servidor de Juego");
define('LANG_clone', "Clonar");
define('LANG_unassign', "Quitar");
define('LANG_access_rights', "Derechos de acceso");
define('LANG_assigned_homes', "Homes asignadas actualmente");
define('LANG_assign', "Asignar");
define('LANG_allow_updates', "Permitir actualizar");
define('LANG_allow_updates_info', "Permite al usuario lanzar una actualizacion si cabe.");
define('LANG_allow_file_management', "Allow File Management");
define('LANG_allow_file_management_info', "Permite que el usuario modifique archivos a traves del modulo de gestion de archivos");
define('LANG_allow_parameter_usage', "Permitir comandos personalizados");
define('LANG_allow_parameter_usage_info', "Permite el uso de comandos personalizados en el arranque.");
define('LANG_allow_extra_params', "Permite parametros extra");
define('LANG_allow_extra_params_info', "Permite incorporar parameteros extra.");
define('LANG_allow_ftp', "Permite FTP");
define('LANG_allow_ftp_info', "Muestra al usuario el acceso al FTP.");
define('LANG_allow_custom_fields', "Permite campos personalizados");
define('LANG_allow_custom_fields_info', "Permite que el usuario pueda editar los campos personalizados.");
define('LANG_select_home', "Seleccionar Home para asignar");
define('LANG_assign_new_home_to_user', "Asignar nueva Home al usuario %s");
define('LANG_assign_new_home_to_group', "Asignar nueva Home al grupo %s");
define('LANG_assigned_home_to_user', "Asignado el home (ID: %d) al usuario %s.");
define('LANG_failed_to_assign_home_to_user', "Error al asignar el home (ID: %d) al usuario %s.");
define('LANG_assigned_home_to_group', "Asignado el home (ID: %d) al grupo %s.");
define('LANG_unassigned_home_from_user', "Quitado el home (ID: %d) del usuario %s.");
define('LANG_unassigned_home_from_group', "Quitado el home (ID: %d) del grupo %s.");
define('LANG_no_homes_assigned_to_user', "No se asigno home al usuario %s.");
define('LANG_no_homes_assigned_to_group', "No se asigno home al grupo %s.");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_user', "No hay mas servidores disponibles para asignar a este usuario");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_group', "No hay mas servidores disponibles para asignar a este grupo");
define('LANG_you_can_add_a_new_game_server_from', "Puede añadir mas servidores desde %s");
define('LANG_no_remote_servers_available_please_add_at_least_one', "Por favor, agregue almenos un servidor remoto.");
define('LANG_cloning_of_home_failed', "Error clonando el home con id '%s'.");
define('LANG_no_mods_to_clone', "No tiene mod(s) para este juego aun. Ninguno sera clonado.");
define('LANG_failed_to_add_mod', "Error al agregar mod con id '%s' al home con id '%s'.");
define('LANG_failed_to_update_mod_settings', "Error al actualizar las opciones del mod para el home con id '%s'.");
define('LANG_successfully_cloned_mods', "Mods correctamente clonados para el home con id '%s'.");
define('LANG_successfully_copied_home_database', "Base de datos copiada correctamente.");
define('LANG_copying_home_remotely', "Copiando el home en el servidor remoto '%s' a '%s'.");
define('LANG_cloning_home', "Clonando el mod llamado '%s'");
define('LANG_current_home_path', "Carpeta del home original");
define('LANG_current_home_path_info', "La carpeta actual del home a copiar al servidor remoto.");
define('LANG_clone_home', "Clonar Home");
define('LANG_new_home_name', "Nombre del Home de destino");
define('LANG_new_home_path', "Carpeta del Home de destino");
define('LANG_agent_ip', "IP del servidor de destino");
define('LANG_game_server_copy_is_running', "La copia esta en marcha.");
define('LANG_game_server_copy_was_successful', "La copia esta se completo satisfactoriamente.");
define('LANG_game_server_copy_failed_with_return_code', "La copia fallo y el codigo de error fue: %s");
define('LANG_clone_mods', "Clonar Mods.");
define('LANG_game_server_owner', "DueÃ±o del servidor.");
define('LANG_the_name_of_the_server_to_help_users_to_identify_it', "El nombre del servidor para ayudar a identificarlo.");
define('LANG_ips_and_ports_used_in_this_home', "Pares de IP:puerto usados en esta servidor");
define('LANG_note_ips_and_ports_are_not_cloned', "Nota: Las direcciones IP no se copiaran.");
define('LANG_mods_and_settings_for_this_game_server', "Mods y configuraciones para este servidor");
define('LANG_sure_to_delete_serverid_from_remoteip_and_directory', "Esta seguro de eliminar el servidor con ID %s del servidor remoto en %s y en el directorio %s");
define('LANG_yes_and_delete_the_files', "Si, y eliminar los archivos y directorios tambien.");
define('LANG_failed_to_remove_gamehome_from_database', "No se pudo eliminar el servidor de la base de datos.");
define('LANG_successfully_deleted_game_server_with_id', "Se elimino el servidor con ID %s.");
define('LANG_failed_to_remove_ftp_account_from_remote_server', "No se pudo eliminar la cuenta FTP del servidor remoto.");
define('LANG_remove_it_anyway', "Eliminar de todos modos");
define('LANG_sucessfully_deleted', "Eliminado correctamente.");
define('LANG_the_agent_had_a_problem_deleting', "El agente tuvo un problema mientras borraba %s. Por favor, compruebe el log del agente.");
define('LANG_connection_timeout_or_problems_reaching_the_agent', "Tiempo de espera de la conexión o problemas al llegar al agente");
define('LANG_does_not_exist_yet', "No existe todavia.");
define('LANG_update_settings', "Actualizar configuración");
define('LANG_settings_updated', "Configuración actualizada.");
define('LANG_selected_path_already_in_use', "La carpeta seleccionada está en uso.");
define('LANG_browse', "Explorar");
define('LANG_cancel', "Cancelar");
define('LANG_set_this_path', "Seleccionar esta carpeta");
define('LANG_select_home_path', "Seleccionar carpeta inicial");
define('LANG_folder', "Carpeta");
define('LANG_owner', "Usuario");
define('LANG_group', "Grupo");
define('LANG_level_up', "Subir un nivel");
define('LANG_level_up_info', "Volver a la carpeta anterior.");
define('LANG_add_folder', "Añadir carpeta");
define('LANG_add_folder_info', "Escriba aquí el nombre para la nueva carpeta, después haga click sobre el icono.");
define('LANG_valid_user', "Por favor especifica un usuario valido.");
define('LANG_valid_group', "Por favor especifica un grupo valido.");
define('LANG_set_affinity', "Establecer afinidad del servidor");
define('LANG_cpu_affinity_info', "Seleccione los núcleo(s) de CPU que quiera asignar al servidor de juego.");
define('LANG_expiration_date_changed', "Se ha cambiado la fecha de caducidad de la casa seleccionada.");
define('LANG_expiration_date_could_not_be_changed', "La fecha de caducidad para el hogar seleccionado no se puede cambiar.");
define('LANG_search', "Buscar");
define('LANG_ftp_account_username_too_long', "El nombre de usuario FTP es demasiado largo. Prueba con un nombre no mayor de 20 caracteres.");
define('LANG_ftp_account_password_too_long', "La contraseña de usuario FTP es demasiado larga. Prueba con una contraseña no mayor de 20 caracteres.");
define('LANG_other_servers_exist_with_path_please_change', "Existen otras Home con la misma ruta. Se recomienda (aunque no se requiere) que cambie la ruta por otra que no esté en uso.");
define('LANG_change_access_rights_for_selected_servers', "Cambiar los derechos de acceso para los servidores seleccionados");
?>