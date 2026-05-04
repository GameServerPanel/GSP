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

define('LANG_add_new_remote_host', "Adicionar novo host remoto");
define('LANG_configured_remote_hosts', "Host remoto configurado");
define('LANG_remote_host', "Host remoto");
define('LANG_remote_host_info', "O host remoto deve ser um nome de host pingable!");
define('LANG_remote_host_port', "Porta de host remoto");
define('LANG_remote_host_port_info', "A porta que esta em escuta pelo Painel Agent no host remoto. Padrão: 12679.");
define('LANG_remote_host_name', "Nome do host remoto");
define('LANG_ogp_user', "OGP Agent Username");
define('LANG_remote_host_name_info', "O nome do host remoto é usado para ajudar os usuários a identificar seus servidores.");
define('LANG_add_remote_host', "Adicionar host remoto");
define('LANG_remote_encryption_key', "Chave de criptografia remota");
define('LANG_remote_encryption_key_info', "A chave de criptografia remota é usada para criptografar os dados entre o Painel e o Agente. Essa chave deve ser igual em ambos os lados.");
define('LANG_server_name', "Nome do servidor");
define('LANG_agent_ip_port', "Agent IP:Porta");
define('LANG_agent_status', "Status Agent");
define('LANG_ips', "IPs");
define('LANG_add_more_ips', "Se você deseja inserir mais IPs, pressione 'Set IPs' quando todos os campos estiverem cheios um novo campo vazio aparecerá.");
define('LANG_encryption_key_mismatch', "A chave de criptografia não coincide com o agente. Verifique novamente a configuração do seu agente");
define('LANG_no_ip_for_remote_host', "Você precisa adicionar pelo menos um (1) endereço IP para cada host remoto.");
define('LANG_note_remote_host', "Um host remoto é um servidor onde o Painel Agent está funcionando. Cada host pode ter um número múltiplo de endereços IP nos quais os usuários podem ligar servidores.");
define('LANG_ip_administration', "Server &amp; IP Administration :: Open Game Panel");
define('LANG_unknown_error', "Erro desconhecido - status_chk returned");
define('LANG_remote_host_user_name', "Usuário UNIX");
define('LANG_remote_host_user_name_info', "Nome de usuário onde o agente está sendo executado. Exemplo: Jonhy");
define('LANG_remote_host_ftp_ip', "FTP IP");
define('LANG_remote_host_ftp_ip_info', "O servidor FTP <b>IP</b> para o atual Agente.");
define('LANG_remote_host_ftp_port', "Porta FTP");
define('LANG_remote_host_ftp_port_info', "O servidor FTP <b>port</b> para o atual Agente.");
define('LANG_view_log', "Exibir Log");
define('LANG_status', "Estado");
define('LANG_stop_firewall', "Pare o firewall");
define('LANG_start_firewall', "Iniciar Firewall");
define('LANG_seconds', "Segundos");
define('LANG_reboot', "Reinicialização remota do servidor");
define('LANG_restart', "Restart Agent");
define('LANG_confirm_reboot', "Tem certeza de que deseja reiniciar remotamente todo o servidor físico chamado '%s'?");
define('LANG_confirm_restart', "Tem certeza de que deseja reiniciar o agente chamado '%s'?");
define('LANG_restarting', "A reiniciar o agente... Por Favor Aguarde.");
define('LANG_restarted', "Agente reiniciado com sucesso.");
define('LANG_reboot_success', "O servidor chamado '%s' foi reiniciado com sucesso. Você não poderá acessar o servidor até que ele seja inicializado com sucesso.");
define('LANG_invalid_remote_host_id', "ID de host remoto inválido '%s' fornecido.");
define('LANG_remote_host_removed', "O host remoto chamado '%s' foi removido com sucesso.");
define('LANG_editing_remote_server', "Editando o servidor remoto chamado '%s'");
define('LANG_remote_server_settings_changed', "Mudou as configurações para o servidor remoto '%s' com sucesso.");
define('LANG_save_settings', "Salvar configurações");
define('LANG_set_ips', "Definir IPs");
define('LANG_remote_ip', "IP remoto");
define('LANG_remote_ips_for', "IPs para servidores de jogos usarem no Servidor Agent '%s'");
define('LANG_ips_set_for_server', "Os IPs definidos para o servidor chamados '%s' com sucesso.");
define('LANG_could_not_remove_ip', "Não foi possível remover os antigos IP do banco de dados.");
define('LANG_could_add_ip', "Poderia adicionar IP do servidor remoto ao banco de dados.");
define('LANG_areyousure_removeagent', "Tem certeza de que deseja remover o agente chamado");
define('LANG_areyousure_removeagent2', "e todos os diretorios relacionadas a ela a partir do banco de dados do painel?");
define('LANG_error_while_remove', "Ocorreu um erro ao remover o servidor remoto.");
define('LANG_add_ip', "Adicionar IP");
define('LANG_remove_ip', "Remover IP");
define('LANG_edit_ip', "Edite o IP");
define('LANG_wrote_changes', "Alterações salvas com sucesso.");
define('LANG_there_are_servers_running_on_this_ip', "Existem servidores em execução neste endereço IP.");
define('LANG_enter_ip_host', "Você deve digitar IP para o host remoto.");
define('LANG_enter_valid_ip', "Você deve digitar a porta válida para o host remoto. O valor da porta pode estar entre 0 e 65535, no entanto a recomendação é entre 1024 e 65535.");
define('LANG_could_not_add_server', "Não foi possível adicionar servidor");
define('LANG_to_db', "para o banco de dados.");
define('LANG_added_server', "Servidor adicionado");
define('LANG_with_port', "com porta");
define('LANG_to_db_succesfully', "para o banco de dados com sucesso.");
define('LANG_unable_discover', "Não é possível descobrir os IPs automaticamente");
define('LANG_set_ip_manually', "Você terá que configurá-los manualmente.");
define('LANG_found_ips', "IPs encontrados");
define('LANG_for_remote_server', "Para o servidor remoto.");
define('LANG_failed_add_ip', "Falha ao adicionar IP");
define('LANG_timeout', "Tempo esgotado");
define('LANG_timeout_info', "O limite de tempo em segundos para obter a resposta deste Agente.");
define('LANG_use_nat', "Usar NAT");
define('LANG_use_nat_info', "Ative se o seu servidor remoto estiver usando regras NAT. Use essa configuração se os servidores do jogo estiverem sendo executados em endereços IP internos da LAN, para que o painel use seu endereço IP remoto real para consultar os servidores de jogos.");
define('LANG_arrange_ports', "Organizar portas");
define('LANG_assign_new_ports_range_for_ip', "Atribuir uma nova faixa de portas para IP %s");
define('LANG_assigned_port_ranges_for_ip', "Faixas de portas atribuídas para IP %s");
define('LANG_assigned_ports_for_ip', "Portas atribuídas para IP %s");
define('LANG_unspecified_game_types', "Tipos de jogos não especificados");
define('LANG_start_port', "Porta de início:");
define('LANG_end_port', "Porta final:");
define('LANG_port_increment', "Aumento da porta:");
define('LANG_total_assignable_ports', "Total de portas atribuíveis:");
define('LANG_available_range_ports', "Portas de alcance disponíveis:");
define('LANG_assign_range', "Atribuir alcance");
define('LANG_edit_range', "Editar distancia");
define('LANG_delete_range', "Apagar distancia");
define('LANG_home_id', "Home ID");
define('LANG_home_path', "Caminho Home");
define('LANG_game_type', "Tipo de jogo");
define('LANG_port', "Porta");
define('LANG_invalid_values', "Valores inválidos.");
define('LANG_ports_in_range_already_arranged', "Portas na faixa já arranjados.");
define('LANG_ports_range_already_configured_for', "A gama de portas já está configurada para %s.");
define('LANG_ports_range_added_successfull_for', "O intervalo de portas foi adicionado com sucesso para %s.");
define('LANG_ports_range_deleted_successfull', "Escala de portas excluída com sucesso.");
define('LANG_ports_range_edited_successfull_for', "O intervalo de portas foi editado com sucesso para %s.");
define('LANG_editing_firewall_for_remote_server', "Editando o Firewall para o servidor remoto chamado '%s'");
define('LANG_default_allowed', "Permitido por padrão");
define('LANG_allow_port_command', "Permitir comando de porta");
define('LANG_deny_port_command', "Regeitar o comando da porta");
define('LANG_allow_ip_port_command', "Permitir IP: comando de porta");
define('LANG_deny_ip_port_command', "Negar IP: comando de porta");
define('LANG_enable_firewall_command', "Habilitar o comando de firewall");
define('LANG_disable_firewall_command', "Desativar o comando do firewall");
define('LANG_get_firewall_status_command', "Obter o comando de status do firewall");
define('LANG_reset_firewall_command', "Redefinir o comando de firewall");
define('LANG_firewall_status', "Estado da Firewall");
define('LANG_save_firewall_settings', "Salvar configurações de firewall");
define('LANG_reset_firewall', "Redefinir Firewall");
define('LANG_firewall_settings', "Configurações do Firewall");
define('LANG_display_public_ip', "Exibir IP público");
define('LANG_ips_can_be_internal_external', "Digite os endereços IP utilizáveis.&nbsp; Endereços IP públicos e endereços IP internos da LAN (para configurações NAT) podem ser usados.");
?>
