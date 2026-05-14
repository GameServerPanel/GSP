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

define('LANG_error', "Erro");
define('LANG_title', "TeamSpeak 3 Web Interface");
define('LANG_update_available', "<h3>Atenção: uma nova versão (v%1) deste software está disponível em <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Sair");
define('LANG_head_vserver_switch', "Alterar vServer");
define('LANG_head_vserver_overview', "Visão geral do vServer");
define('LANG_head_vserver_token', "Gerenciamento de Tokens");
define('LANG_head_vserver_liveview', "Visualização ao vivo");
define('LANG_e_fill_out', "Preencha todos os campos obrigatórios.");
define('LANG_e_upload_failed', "Envio sem sucesso.");
define('LANG_e_server_responded', "O servidor respondeu:");
define('LANG_e_conn_serverquery', "Não foi possível criar o acesso ServerQuery.");
define('LANG_e_conn_vserver', "Não foi possível escolher o servidor virtual.");
define('LANG_e_session_timedout', "Sessão expirada.");
define('LANG_js_error', "Erro");
define('LANG_js_ajax_error', "Ocorreu algum erro no AJAX: %1.");
define('LANG_js_confirm_server_stop', "Você realmente quer PARAR o servidor? #%1?");
define('LANG_js_confirm_server_delete', "Você realmente quer APAGAR o servidor #%1?");
define('LANG_js_notice_server_deleted', "O servidor %1 foi excluído com êxito.\nA página de visão geral ficará recarregada agora.");
define('LANG_js_prompt_banduration', "Duração em horas (0=ilimitado):");
define('LANG_js_prompt_banreason', "Razão (opcional): ");
define('LANG_js_prompt_msg_to', "Mensagem de texto para %1 #%2: ");
define('LANG_js_prompt_poke_to', "Poke Mensagem para o cliente #% 1:");
define('LANG_js_prompt_new_propvalue', "Novo valor para '%1': ");
define('LANG_n_server_responded', "O servidor respondeu: ");
define('LANG_login_serverquery', "ServerQuery Login");
define('LANG_login_name', "Nome de usuário");
define('LANG_login_password', "Password");
define('LANG_login_submit', "Login");
define('LANG_vsselect_headline', "Seleção do vServer");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Nome");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Porta");
define('LANG_vsselect_state', "Estado");
define('LANG_vsselect_clients', "Clientes");
define('LANG_vsselect_uptime', "Tempo de atividade");
define('LANG_vsselect_choose', "Selecione");
define('LANG_vsselect_start', "Começar");
define('LANG_vsselect_stop', "Parar");
define('LANG_vsselect_delete', "EXCLUIR");
define('LANG_vsselect_new_headline', "Crie um novo servidor virtual");
define('LANG_vsselect_new_servername', "Nome do servidor");
define('LANG_vsselect_new_slots', "Slot do cliente");
define('LANG_vsselect_new_create', "Criar");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> foi criado com sucesso.");
define('LANG_vsselect_new_added_generated', "O token gerado é:");
define('LANG_vsoverview_virtualserver', "Servidor virtual");
define('LANG_vsoverview_information_head', "informação");
define('LANG_vsoverview_connection_head', "Conexão");
define('LANG_vsoverview_info_general_head', "Configurações Gerais");
define('LANG_vsoverview_info_servername', "Nome do servidor");
define('LANG_vsoverview_info_host', "Anfitrião");
define('LANG_vsoverview_info_state', "Estado");
define('LANG_vsoverview_info_state_port', "Porta");
define('LANG_vsoverview_info_uptime', "Tempo de atividade");
define('LANG_vsoverview_info_welcomemsg', "Bem-vindo <br /> mensagem");
define('LANG_vsoverview_info_hostmsg', "Mensagem de anfitrião");
define('LANG_vsoverview_info_hostmsg_mode_output', "saída");
define('LANG_vsoverview_info_hostmsg_mode_0', "Nenhum");
define('LANG_vsoverview_info_hostmsg_mode_1', "No log de bate-papo");
define('LANG_vsoverview_info_hostmsg_mode_2', "janela");
define('LANG_vsoverview_info_hostmsg_mode_3', "Window + Disconnect");
define('LANG_vsoverview_info_req_security', "Nível de segurança");
define('LANG_vsoverview_info_req_securitylvl', "exigido");
define('LANG_vsoverview_info_hostbanner_head', "Hostbanner");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Endereço da imagem");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Hostbutton URL");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Aviso sobre");
define('LANG_vsoverview_info_antiflood_kick', "Kikar ligado");
define('LANG_vsoverview_info_antiflood_ban', "Banimento ligado");
define('LANG_vsoverview_info_antiflood_banduration', "Tempo de Banimento");
define('LANG_vsoverview_info_antiflood_decrease', "Diminuir");
define('LANG_vsoverview_info_antiflood_points', "pontos");
define('LANG_vsoverview_info_antiflood_in_seconds', "segundos");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Pontos por segundo\"Points per tick\"");
define('LANG_vsoverview_conn_total_head', "Total");
define('LANG_vsoverview_conn_total_packets', "Pacotes");
define('LANG_vsoverview_conn_total_bytes', "bytes");
define('LANG_vsoverview_conn_total_send', "enviado");
define('LANG_vsoverview_conn_total_received', "recebido");
define('LANG_vsoverview_conn_bandwidth_head', "Largura de banda");
define('LANG_vsoverview_conn_bandwidth_last', "último");
define('LANG_vsoverview_conn_bandwidth_second', "segundo");
define('LANG_vsoverview_conn_bandwidth_minute', "minuto");
define('LANG_vsoverview_conn_bandwidth_send', "enviado");
define('LANG_vsoverview_conn_bandwidth_received', "recebido");
define('LANG_vstoken_token_virtualserver', "Servidor virtual");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Tipo de grupo");
define('LANG_vstoken_token_id1', "Grupo de Servidores/<br />Grupo de canais");
define('LANG_vstoken_token_id2', "(Canal)");
define('LANG_vstoken_token_tokencode', "Código Token");
define('LANG_vstoken_token_delete', "Excluir");
define('LANG_vstoken_new_head', "Crie um novo token");
define('LANG_vstoken_new_create', "Gerar");
define('LANG_vstoken_new_tokentype', "Tipo de token:");
define('LANG_vstoken_new_servergroup', "Grupo de Servidores");
define('LANG_vstoken_new_channelgroup', "Grupo de canais");
define('LANG_vstoken_new_select_group', "Grupo de servidor");
define('LANG_vstoken_new_select_channelgroup', "Grupo de Canal");
define('LANG_vstoken_new_select_channel', "Canal");
define('LANG_vstoken_new_tokentype_0', "Servidor");
define('LANG_vstoken_new_tokentype_1', "Canal");
define('LANG_vstoken_new_added_ok', "Token foi gerado com sucesso.");
define('LANG_vsliveview_server_virtualserver', "Servidor virtual");
define('LANG_vsliveview_server_head', "Visualização ao vivo");
define('LANG_vsliveview_liveview_enable_autorefresh', "Atualização automática");
define('LANG_vsliveview_liveview_tooltip_to_channel', "ao canal #");
define('LANG_vsliveview_liveview_tooltip_switch', "trocar");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Enviar mensagem");
define('LANG_vsliveview_liveview_tooltip_poke', "Cutucar \"poke\"");
define('LANG_vsliveview_liveview_tooltip_kick', "Kickar");
define('LANG_vsliveview_liveview_tooltip_ban', "Banir");
define('LANG_vsoverview_banlist_head', "Lista de banimentos");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Nome");
define('LANG_vsoverview_banlist_uid', "ID único");
define('LANG_vsoverview_banlist_reason', "Razão");
define('LANG_vsoverview_banlist_created', "Criada");
define('LANG_vsoverview_banlist_duration', "Duração");
define('LANG_vsoverview_banlist_end', "Termina");
define('LANG_vsoverview_banlist_unlimited', "ilimitado");
define('LANG_vsoverview_banlist_never', "nunca");
define('LANG_vsoverview_banlist_new_head', "Criar novo ban");
define('LANG_vsoverview_banlist_new_create', "criar");
define('LANG_vsliveview_channelbackup_head', "Backup de canal");
define('LANG_vsliveview_channelbackup_get', "Criar e baixar");
define('LANG_vsliveview_channelbackup_load', "Carregar backup de canal");
define('LANG_vsliveview_channelbackup_load_submit', "Recriar");
define('LANG_vsliveview_channelbackup_new_added_ok', "O backup do canal foi bem-sucedido.");
define('LANG_time_day', "dia");
define('LANG_time_days', "dias");
define('LANG_time_hour', "horas");
define('LANG_time_hours', "horas");
define('LANG_time_minute', "minuto");
define('LANG_time_minutes', "minutos");
define('LANG_time_second', "segundo");
define('LANG_time_seconds', "segundos");
define('LANG_e_2568', "Você não tem direitos suficientes.");
define('LANG_temp_folder_not_writable', "A pasta de templates (%s) não pode ser gravada.");
define('LANG_unassign_from_subuser', "Cancelar atribuição do sub-usuário");
define('LANG_assign_to_subuser', "Atribuir ao sub-usuário.");
define('LANG_select_subuser', "Selecione o sub-usuário.");
define('LANG_no_ts3_servers_assigned_to_account', "Você não possui servidores atribuídos à sua conta.");
define('LANG_change_virtual_server', "Alterar servidor virtual");
define('LANG_change_remote_server', "Alterar Servidor Remoto");
?>