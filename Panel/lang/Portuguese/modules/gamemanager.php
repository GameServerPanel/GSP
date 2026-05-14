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

define('LANG_no_games_to_monitor', "Você não possui nenhum jogo configurado que possa ser exibido.");
define('LANG_status', "Estado");
define('LANG_fail_no_mods', "Não há mods habilitados para este jogo! Você precisa pedir ao seu administrador do painel para adicionar mod(s) ao jogo que lhe foi atribuído.");
define('LANG_no_game_homes_assigned', "Você não tem nenhum servidor atribuído à sua conta.");
define('LANG_select_game_home_to_configure', "Escolha um servidor que deseja configurar");
define('LANG_file_manager', "Gestor de Arquivos");
define('LANG_configure_mods', "Configurar mods");
define('LANG_install_update_steam', "Instalar/Atualizar através do Steam");
define('LANG_install_update_manual', "Instalar/Atualizar manualmente");
define('LANG_assign_game_homes', "Associar servidores");
define('LANG_user', "Usuário");
define('LANG_group', "Grupo");
define('LANG_start', "Iniciar");
define('LANG_ogp_agent_ip', "IP do Server Agent");
define('LANG_max_players', "Máx. Jogadores");
define('LANG_max', "Máx.");
define('LANG_ip_and_port', "IP e Porta");
define('LANG_available_maps', "Mapas Disponíveis");
define('LANG_map_path', "Pasta de Mapas");
define('LANG_available_parameters', "Parâmetros Disponíveis");
define('LANG_start_server', "Iniciar Servidor");
define('LANG_start_wait_note', "A inicialização do servidor pode levar uns breves instantes. Por Favor não faça REFRESH a pagina, não feche a pagina ou encerre o navegador. O próprio PAINEL fará tudo isso por si... Apenas tenha um pouco de paciência se demorar alguns segundos.");
define('LANG_game_type', "Tipo de Jogo");
define('LANG_map', "Mapa");
define('LANG_starting_server', "A iniciar o servidor, por favor aguarde uns instantes...");
define('LANG_starting_server_settings', "A iniciar o servidor com as seguintes configurações");
define('LANG_startup_params', "Parâmetros de inicialização");
define('LANG_startup_cpu', "CPU em que o servidor está em execução");
define('LANG_startup_nice', "Defina a prioridade do server");
define('LANG_game_home', "Caminho raiz");
define('LANG_server_started', "Servidor iniciado com sucesso.");
define('LANG_no_parameter_access', "Você não tem acesso aos parâmetros");
define('LANG_extra_parameters', "Parâmetros Adicionais");
define('LANG_no_extra_param_access', "Você não tem acesso aos parâmetros adicionais");
define('LANG_extra_parameters_info', "Esses parâmetros serão adicionados no final da linha de comando quando o servidor for iniciado.");
define('LANG_game_exec_not_found', "O servidor executado %s não foi encontrado no servidor remoto.");
define('LANG_select_params_and_start', "Selecione os parâmetros de inicialização para o servidor e clique em '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Não há um IP e porta associados para este directório. Se você não tem acesso a edição do mesmo, entre em contacto com um administrador.");
define('LANG_unable_to_get_log', "Falha ao obter resposta do log, status %s.");
define('LANG_server_binary_not_executable', "O executável do servidor não tem permissões de execução. Verifique as permissões de arquivo na pasta do servidor.");
define('LANG_server_not_running_log_found', "O servidor não está em execução, mas um registo de log foi encontrado. NOTA: Esse registo pode não ser relacionado à ultima vez que o servidor foi iniciado.");
define('LANG_ip_port_pair_not_owned', "IP:PORT não é dono deste par.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "O valor maxplayers não não se encontra adequado, o número máximo de slots alcançado foi definido.");
define('LANG_server_running_not_responding', "O servidor está em execução mas não está a responder, <br>pode possivelmente haver algum tipo de problema e talvez você queira");
define('LANG_update_started', "Actualização iniciada, por favor aguarde...");
define('LANG_failed_to_start_steam_update', "Falha ao iniciar a actualização via Steam. Verifique o log do agente para  averiguar com mais detalhes.");
define('LANG_failed_to_start_rsync_update', "Falha ao iniciar o Rsync. Verifique o log do agente para mais detalhes.");
define('LANG_update_completed', "Atualização concluída com sucesso.");
define('LANG_update_in_progress', "Atualização em andamento, por favor aguarde...");
define('LANG_refresh_steam_status', "Atualize o status do Steam");
define('LANG_refresh_rsync_status', "Atualize o status do Rsync");
define('LANG_server_running_cant_update', "Servidor em execução, não é possível actualizá-lo. Por favor,  encerre-o primeiro antes de qualquer actualização.");
define('LANG_xml_steam_error', "O tipo de servidor seleccionado não suporta actualizações via Steam.");
define('LANG_mod_key_not_found_from_xml', "Tecla Mod '%s' não encontrado a partir do arquivo XML");
define('LANG_stop_update', "Pare de actualizar");
define('LANG_statistics', "Estatísticas");
define('LANG_servers', "Servidores");
define('LANG_players', "Jogadores");
define('LANG_current_map', "Mapa Atual");
define('LANG_stop_server', "Parar servidor");
define('LANG_server_ip_port', "IP:Porta do Servidor");
define('LANG_server_name', "Nome do Servidor");
define('LANG_server_id', "ID do Servidor");
define('LANG_player_name', "Nome do Jogador");
define('LANG_score', "Pontuação");
define('LANG_time', "Tempo");
define('LANG_no_rights_to_stop_server', "Você não tem permissões para parar este servidor.");
define('LANG_no_ogp_lgsl_support', "Este servidor (em execução: %s) não tem suporte LGSL no painel e as suas estatísticas não podem ser exibidas.");
define('LANG_server_status', "Servidor em %s é %s.");
define('LANG_server_stopped', "O servidor '%s' foi encerrado.");
define('LANG_if_want_to_start_homes', "Se você quer iniciar os servidores vá para %s.");
define('LANG_view_log', "Verificação de Logs");
define('LANG_if_want_manage', "Se você deseja fazer a gestão dos seus jogos você pode fazê-lo na");
define('LANG_columns', "colunas");
define('LANG_group_users', "Usuários do grupo:");
define('LANG_assigned_to', "Associado a:");
define('LANG_restart_server', "Reiniciar Servidor");
define('LANG_restarting_server', "A reiniciar servidor, por favor aguarde...");
define('LANG_server_restarted', "O servidor '%s' foi reiniciado.");
define('LANG_server_not_running', "O servidor não está em execução.");
define('LANG_address', "Endereço");
define('LANG_owner', "Dono");
define('LANG_operations', "Operações");
define('LANG_search', "Pesquisar");
define('LANG_maps_read_from', "Mapas obtidos do");
define('LANG_file', "arquivo");
define('LANG_folder', "diretório");
define('LANG_unable_retrieve_mod_info', "Falha ao obter informações sobre o mod no banco de dados.");
define('LANG_unexpected_result_libremote', "Resultado inesperado do libremote, por favor informe aos desenvolvedores.");
define('LANG_unable_get_info', "Falha ao obter as informações necessárias para inicializar, cancelando inicialização.");
define('LANG_server_already_running', "O servidor já está em execução. Se você não o vê no Gerenciador de Servidores, pode haver algum tipo de problema e talvez você queira");
define('LANG_already_running_stop_server', "Parar servidor.");
define('LANG_error_server_already_running', "ERRO: Já existe um servidor nesta mesma porta");
define('LANG_failed_start_server_code', "Falha ao iniciar servidor remoto. Código de erro: %s");
define('LANG_disabled', "desactivado ");
define('LANG_not_found_server', "Não foi possível encontrar o servidor com a ID");
define('LANG_rcon_command_title', "Comando RCON");
define('LANG_has_sent_to', "foi enviado para");
define('LANG_need_set_remote_pass', "Você precisa configurar uma senha rcon no");
define('LANG_before_sending_rcon_com', "antes de enviar comandos para ele.");
define('LANG_retry', "Tentar novamente");
define('LANG_page', "página");
define('LANG_server_cant_start', "servidor não pode iniciar");
define('LANG_server_cant_stop', "servidor não pôde parar");
define('LANG_error_occured_remote_host', "Ocorreu um erro no host remoto.");
define('LANG_follow_server_status', "Você pode seguir o status do servidor de");
define('LANG_addons', "Addons");
define('LANG_hostname', "Nome de anfitrião");
define('LANG_rsync_install', "[Instalar Rsync]");
define('LANG_ping', "Ping");
define('LANG_team', "Equipa");
define('LANG_deaths', "Mortes");
define('LANG_pid', "PID");
define('LANG_skill', "Habilidade");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Jogador");
define('LANG_port', "Porta");
define('LANG_rcon_presets', "Predefinições RCON");
define('LANG_update_from_local_master_server', "Atualização do servidor Master Server");
define('LANG_update_from_selected_rsync_server', "Atualização do servidor Rsync selecionado");
define('LANG_execute_selected_server_operations', "Execute as operações selecionadas do servidor ");
define('LANG_execute_operations', "Execute operações");
define('LANG_account_expiration', "Vencimento da conta");
define('LANG_mysql_databases', "Bases de dados MySQL");
define('LANG_failed_querying_server', "* Falha ao consultar o servidor");
define('LANG_query_protocol_not_supported', "* Não há protocolo de consulta no painel que possa ser suportado neste servidor.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Consultas desativadas por configuração: Desativar consultas após: %s, desde que você tem %s servidores.<br>");
define('LANG_presets_for_game_and_mod', "Predefinições RCON para %s e mod %s");
define('LANG_name', "Nome");
define('LANG_command', "RCON&nbsp;Comando");
define('LANG_add_preset', "Adicionar predefinido");
define('LANG_edit_presets', "Editar predefinições");
define('LANG_del_preset', "Apagar");
define('LANG_change_preset', "Mudar");
define('LANG_send_command', "Enviar comando");
define('LANG_starting_copy_with_master_server_named', "Iniciando a cópia com o Master Server chamado '%s'...");
define('LANG_starting_sync_with', "Iniciar sincronização com %s...");
define('LANG_refresh_interval', "Intervalo de atualização do log");
define('LANG_finished_manual_update', "Atualização manual concluída.");
define('LANG_failed_to_start_file_download', "Falha ao iniciar o download de arquivos");
define('LANG_game_name', "Nome do jogo");
define('LANG_dest_dir', "Diretório de destino");
define('LANG_remote_server', "Servidor remoto");
define('LANG_file_url', "URL do arquivo");
define('LANG_file_url_info', "O URL do arquivo foi carregado e descompactado para o diretório.");
define('LANG_dest_filename', "Nome do Destino");
define('LANG_dest_filename_info', "O nome do arquivo para o destino de arquivo.");
define('LANG_update_server', "Atualizar servidor");
define('LANG_unavailable', "Indisponível");
define('LANG_upload_map_image', "Carregar imagem do mapa");
define('LANG_upload_image', "Enviar Imagem");
define('LANG_jpg_gif_png_less_than_1mb', "A imagem deve ser jpg, gif ou png e tamanho inferior a 1 MB.");
define('LANG_check_dev_console', "Erro durante o upload do arquivo, verifique o console do desenvolvedor no navegador.");
define('LANG_uploaded_successfully', "Carregado com sucesso.");
define('LANG_cant_create_folder', "Não foi possível criar a pasta:<br><b>%s</b>");
define('LANG_cant_write_file', "Não foi possível gravar o arquivo:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Excedeu a diretriz PHP.<br><b>%s</b>.");
define('LANG_unknown_errors', "Erros desconhecidos.");
define('LANG_directory', "Diretório");
define('LANG_view_player_commands', "Ver comandos do jogador");
define('LANG_hide_player_commands', "Ocultar Comandos do Jogador");
define('LANG_no_online_players', "Não há jogadores online.");
define('LANG_invalid_game_mod_id', "ID invalido de Jogo/Mod especificado.");
define('LANG_auto_update_title_popup', "STEAM - atualização automática de Link");
define('LANG_auto_update_popup_html', "<p>Use o link abaixo para verificar e atualizar automaticamente seu servidor de jogos via Steam, se necessário.&nbsp; Você pode consultá-lo usando um cronjob ou iniciar manualmente o processo.</p>");
define('LANG_api_links_popup_html', "<p>Selecione uma ação que você gostaria de realizar usando a API do GamePanel para este servidor de jogos.&nbsp; Em seguida, use o link abaixo para realizar a ação desejada. Você pode executar a ação desejada usando um cronjob ou fazendo-lhe um pedido de REQUEST direto.</p>");
define('LANG_auto_update_copy_me', "Copia");
define('LANG_auto_update_copy_me_success', "Copiado!");
define('LANG_auto_update_copy_me_fail', "Falha ao copiar. Por favor, copie manualmente o link.");
define('LANG_get_steam_autoupdate_api_link', "Link de atualização automática");
define('LANG_show_api_actions', "Mostrar ações da API");
define('LANG_api_links', "Links da API ");
define('LANG_update_attempt_from_nonmaster_server', "Usuario %s tentou atualizar home_id %d De um servidor Non-Master. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Você está tentando atualizar este servidor a partir de um servidor Non-Master.");
define('LANG_cannot_update_from_own_self', "A atualização do servidor local pode não ser executada em um Master Server");
define('LANG_show_server_id', "Mostrar IDs do servidor");
define('LANG_hide_server_id', "Ocultar IDs de servidor");
define('LANG_edit_configuration_files', "Editar arquivos de configuração");
define('LANG_admin', "Administrador");
define('LANG_cid', "CID");
define('LANG_phan', "Fantasma");
define('LANG_sec', "Segundos");
define('LANG_unknown_rsync_mirror', "Você tentou iniciar uma actualização de um destino que não existe.");
define('LANG_custom_fields', "Custom Fields");
?>
