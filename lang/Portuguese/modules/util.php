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

define('OGP_LANG_module_name', "Utilidades");
define('OGP_LANG_ping', "Ping");
define('OGP_LANG_traceroute', "Traceroute");
define('OGP_LANG_network_tools', "Ferramentas de rede");
define('OGP_LANG_sourcemod_admins', "Sourcemod Admins");
define('OGP_LANG_steam_converter', "Conversor do SteamID");
define('OGP_LANG_your_ip', "Teu endereço de IP:");
define('OGP_LANG_loading_agents', "A carregar os agentes online ...");
define('OGP_LANG_loading_failed', "Falhou o carregamento dos agentes.");
define('OGP_LANG_agents_offline', "Todos os Agentes estão offline.");
define('OGP_LANG_no_commands', "Desculpe, sua conta de usuário não possui comandos disponíveis.");
define('OGP_LANG_remote_target', "Endereço IP alvo:");
define('OGP_LANG_command', "Comando:");
define('OGP_LANG_select_agent', "Selecione Agente:");
define('OGP_LANG_chdir_failed', "Erro: chdir() retornou falso.");
define('OGP_LANG_agent_invalid', "Foi especificado um agente invalido.");
define('OGP_LANG_networktools_agent_offline', "Não é possível executar seu comando no Agente selecionado, porque está offline.");
define('OGP_LANG_target_empty', "Nenhum alvo remoto dado.");
define('OGP_LANG_command_empty', "Nenhum comando selecionado.");
define('OGP_LANG_command_unavilable', "O comando selecionado não está disponível no agente selecionado.");
define('OGP_LANG_target_invalid', "IP/host inválido inserido.");
define('OGP_LANG_exec_failed', "Tempo de espera enquanto esperava uma resposta.");
define('OGP_LANG_command_no_access', "Você não tem acesso a este comando. Este incidente será registrado.");
define('OGP_LANG_command_hacking_attempt', "Foram inseridos caracteres na lista negra. Este incidente será registrado.");
define('OGP_LANG_command_bad_characters', "Tentou executar um comando com caracteres mal-intencionados. Entrada recebida: %s %s");
define('OGP_LANG_command_no_permissions', "Tentou executar um comando com permissões insuficientes. Entrada recebida: %s %s");
define('OGP_LANG_command_executed', "Enviou com êxito o seguinte comando: %s %s");
define('OGP_LANG_no_servers', "Você não possui servidores atribuídos à sua conta.");
define('OGP_LANG_select_server', "Selecione Servidor:");
define('OGP_LANG_select_server_option', "Selecione...");
define('OGP_LANG_steamid', "Steam ID:");
define('OGP_LANG_immunity', "Immunity:");
define('OGP_LANG_sourcemod_perms', "Sourcemod Permissions:");
define('OGP_LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('OGP_LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('OGP_LANG_sourcemod_flag_a', "Acesso ao slot reservado.");
define('OGP_LANG_sourcemod_flag_b', "Administrador genérico; Necessário para administradores.");
define('OGP_LANG_sourcemod_flag_c', "Chute outros jogadores.");
define('OGP_LANG_sourcemod_flag_d', "Banir outros jogadores.");
define('OGP_LANG_sourcemod_flag_e', "Remova os ban's.");
define('OGP_LANG_sourcemod_flag_f', "Matar/prejudicar outros jogadores.");
define('OGP_LANG_sourcemod_flag_g', "Mude o mapa ou os principais recursos de jogabilidade.");
define('OGP_LANG_sourcemod_flag_h', "Mude a maioria dos CVARs.");
define('OGP_LANG_sourcemod_flag_i', "Execute arquivos de configuração.");
define('OGP_LANG_sourcemod_flag_j', "Privilégios especiais de chat");
define('OGP_LANG_sourcemod_flag_k', "Comece ou crie votações.");
define('OGP_LANG_sourcemod_flag_l', "Defina uma senha no servidor.");
define('OGP_LANG_sourcemod_flag_m', "Use os comandos RCON.");
define('OGP_LANG_sourcemod_flag_n', "Altere sv_cheats ou use comandos de trapaça.");
define('OGP_LANG_sourcemod_flag_o', "Grupo personalizado 1.");
define('OGP_LANG_sourcemod_flag_p', "Grupo personalizado 2.");
define('OGP_LANG_sourcemod_flag_q', "Personalizado Grupo 3.");
define('OGP_LANG_sourcemod_flag_r', "Grupo personalizado 4.");
define('OGP_LANG_sourcemod_flag_s', "Grupo personalizado 5.");
define('OGP_LANG_sourcemod_flag_t', "Grupo personalizado 6.");
define('OGP_LANG_rcon_reload_admins_failed', "Falha ao recarregar o cache do administrador via RCON; Está online?");
define('OGP_LANG_reload_admins_failed', "Falha ao recarregar o cache do administrador; \"sm_reloadadmins\" é um comando desconhecido.");
define('OGP_LANG_reload_admins_success', "Adicionou %s de forma bem-sucedida a admins_simple.ini e recarregou o cache do administrador.");
define('OGP_LANG_add_success_no_rcon', "Acrescentou %s ao seu arquivo admins_simple.ini, mas não foi possível recarregar o cache do administrador.");
define('OGP_LANG_writefile_error', "Houve um erro desconhecido ao escrever para: %s");
define('OGP_LANG_remotefile_nonexistent', "Não foi possível adicionar um novo administrador. Arquivo de administração: %s não existem neste servidor.");
define('OGP_LANG_empty_flag_list', "Você não selecionou nenhum sinalizador de administração.");
define('OGP_LANG_invalid_steam_format', "O SteamID que você digitou não corresponde ao padrão exigido.");
define('OGP_LANG_selected_server_offline', "Não é possível adicionar um administrador, o agente que controla o servidor selecionado está desconectado.");
define('OGP_LANG_malformed_form', "Você enviou um formulário com elementos ocultos malformados - incapaz de adicionar um administrador.");
define('OGP_LANG_empty_form_data', "Preencha todos os elementos do formulário.");
define('OGP_LANG_server_not_selected', "Você não selecionou um servidor.");
define('OGP_LANG_invalid_steamid', "Você digitou um ID de Steam inválido.");
define('OGP_LANG_invalid_immunity', "Você inseriu um valor de imunidade inválido.");
define('OGP_LANG_submit', "Submeter");
define('OGP_LANG_post_failed', "A ação POST falhou. Não foi possível recuperar uma resposta.");
define('OGP_LANG_amx_mod_admins', "Administradores AMX mod X");
define('OGP_LANG_amx_login_type', "Tipo de Login");
define('OGP_LANG_amx_login_steamid', "Steam ID");
define('OGP_LANG_amx_login_nick_pass', "Nickname + Password");
define('OGP_LANG_nickname', "Nickname");
define('OGP_LANG_amx_mod_perms', "Permissões AMX mod X ");
define('OGP_LANG_amx_mod_perm_root', "Todas as Flags AMX mod X");
define('OGP_LANG_amx_mod_perm_custom', "Flags customizadas AMX mod X");
define('OGP_LANG_amx_mod_flag_a', "Imunidade (Não pode ser expulso/banido/chacinado/bofeteado e afetado por outros comandos) ");
define('OGP_LANG_amx_mod_flag_b', "reserva (pode entrar em slots reservados)");
define('OGP_LANG_amx_mod_flag_c', "Comando amx_kick");
define('OGP_LANG_amx_mod_flag_d', "comandos amx_ban e amx_unban");
define('OGP_LANG_amx_mod_flag_e', "comandos amx_slay e amx_slap");
define('OGP_LANG_amx_mod_flag_f', "comando amx_map");
define('OGP_LANG_amx_mod_flag_g', "comando amx_cvar (nem todos os cvars estão disponiveis)");
define('OGP_LANG_amx_mod_flag_h', "comando amx_cfg");
define('OGP_LANG_amx_mod_flag_i', "amx_chat e outros comandos de chat");
define('OGP_LANG_amx_mod_flag_j', "amx_vote e outros comandos de votação");
define('OGP_LANG_amx_mod_flag_k', "acesso à cvar sv_password (pelo comando amx_cvar)");
define('OGP_LANG_amx_mod_flag_l', "acesso ao comando amx_rcon e rcon_password cvar (pelo comando amx_cvar)");
define('OGP_LANG_amx_mod_flag_m', "nível customizado A (para plugins adicionais)");
define('OGP_LANG_amx_mod_flag_n', "nível customizado B");
define('OGP_LANG_amx_mod_flag_o', "nível customizado C");
define('OGP_LANG_amx_mod_flag_p', "nível customizado D ");
define('OGP_LANG_amx_mod_flag_q', "nível customizado E");
define('OGP_LANG_amx_mod_flag_r', "nível customizado F ");
define('OGP_LANG_amx_mod_flag_s', "nível customizado G");
define('OGP_LANG_amx_mod_flag_t', "nível customizado H");
define('OGP_LANG_amx_mod_flag_u', "menu de acesso");
?>