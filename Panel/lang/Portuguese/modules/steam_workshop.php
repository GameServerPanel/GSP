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
define('LANG_game', "Jogo");
define('LANG_select_mod', "Selecionar Mod");
define('LANG_manual_workshop_mod_id', "Manual Workshop mod ID**");
define('LANG_manual_workshop_mod_id_info', "Você encontrará o mod id na URL do mod, por exemplo 1379153273 para o painel solar do ARK: Survival Evolved. Você pode instalar vários mods separando-os por coma.");
define('LANG_update_in_progress', "Atualização em Progresso");
define('LANG_refresh_steam_workshop_status', "Atualizar status da Steam Workshop");
define('LANG_update_completed', "Atualização concluida");
define('LANG_mod_does_not_belong_to_workshop', "O mod %s não pertence ao Workshop");
define('LANG_mod_installation_started', "Instalação do mod começou");
define('LANG_failed_to_start_steam_workshop', "Falha ao começar Steam Workshop");
define('LANG_connection_error', "Erro de conexão");
define('LANG_install_mod', "Instalar mods");
define('LANG_show_mod_info', "Mostrar informação dos mods");
define('LANG_select_game', "Selecionar Jogo");
define('LANG_save_config', "Salvar Config");
define('LANG_mod_key_not_found_from_xml', "Chave de modificação %s não encontrado no XML.");
define('LANG_workshop_id', "Workshop ID");
define('LANG_workshop_id_info', "Você encontrará o ID da Oficina no URL do Workshop, por exemplo 440900 para Conan Exiles");
define('LANG_mods_path', "Caminho dos Mods ");
define('LANG_mods_path_info', "O caminho relativo para a pasta mods.");
define('LANG_regex', "Regex");
define('LANG_regex_info', "Uma expressão regular que corresponde aos mods no arquivo de configuração");
define('LANG_mods_backreference_index', "Índice de retrocedência de mods");
define('LANG_mods_backreference_index_info', "A posição da referência de retorno da parte da regex que corresponde à lista de mods, começando por 0.");
define('LANG_variable', "Variável ");
define('LANG_variable_info', "A variável que contém a lista de mods, se houver.");
define('LANG_place_after', "Colocar Depois");
define('LANG_place_after_info', "A secção do arquivo de configuração onde a lista de mods aparece, se houver. Ele será adicionado ao arquivo de configuração, se ainda não existir. Se a variável dada não estiver presente, ela será colocada na linha após esta secção.");
define('LANG_mod_string', "Mod String");
define('LANG_mod_string_info', "A string que representa o mod na lista de mod. Substituições válidas: %workshop_mod_id%, %first_file% (primeiro arquivo é o primeiro arquivo encontrado na pasta mod baixada pelo SteamCMD)");
define('LANG_string_separator', "Separador de String");
define('LANG_string_separator_info', "O caractere que separa os mods no arquivo de configuração, por exemplo, novo caractere de linha (\\n) ou vírgula (,).");
define('LANG_filepath', "Caminho do arquivo");
define('LANG_filepath_info', "O caminho da configuração onde os mods devem ser listados.");
define('LANG_post_install', "Postinstall Script");
define('LANG_post_install_info', "Os comandos necessários no bash para mover os mods para a pasta mods. Substituições válidas: %mods_full_path% (o caminho completo para a pasta de mods do Wokshop), %workshop_mod_id%, %first_file% (primeiro arquivo é o primeiro arquivo encontrado na pasta mod baixada pelo SteamCMD)");
define('LANG_install_mods', "Instalar Mods");
define('LANG_uninstall_mods', "Desinstalar Mods");
define('LANG_failed_uninstalling_mod', "Falha ao desinstalar mod %s ");
define('LANG_uninstall', "Desinstalar Script");
define('LANG_uninstall_info', "Este é o script chamado quando um mod é desinstalado, substituições válidas: %mods_full_path% (o caminho completo para a pasta mods do wokshop), %mod_string% (mod string é o nome listado no arquivo de configuração para este mod).");
define('LANG_remove_mods', "Remover Mods");
define('LANG_do_not_close_this_page_while_mods_are_being_installed', "Não feche esta pagina enquanto os mods estão a ser instalados.");
define('LANG_no_game_server_selected', "Nenhum servidor de jogo selecionado");
define('LANG_there_are_no_mods_installed_on_this_game_server', "Não tem mods instalados neste servidor de jogo");
define('LANG_workshop_configuration_not_found', "Configuração da oficina não encontrada");
define('LANG_download_method', "Método de Download");
define('LANG_anonymous_login', "Login Anónimo ");
define('LANG_select_at_least_one_mod_or_enter_mod_id', "Seleciona pelo menos um mod ou introduz um mod ID");
define('LANG_no_game_servers_assigned', "Não tens nenhum servidor atribuído há tua conta.");
?>