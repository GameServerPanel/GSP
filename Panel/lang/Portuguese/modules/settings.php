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

define('LANG_maintenance_mode', "Manutenção");
define('LANG_maintenance_mode_info', "Desativa o painel para usuários comuns. Somente administradores terão acesso com essa opção ativada.");
define('LANG_maintenance_title', "Título para manutenção");
define('LANG_maintenance_title_info', "O título será exibido para os usuários que tentarem acessar o painel durante a manutenção.");
define('LANG_maintenance_message', "Mensagem");
define('LANG_maintenance_message_info', "A mensagem que é dispayed para usuários normais durante a manutenção.");
define('LANG_update_settings', "Salvar alterações");
define('LANG_settings_updated', "Configurações atualizadas com sucesso.");
define('LANG_panel_language', "Idioma do painel");
define('LANG_panel_language_info', "Esta linguagem é a linguagem padrão do painel de usuário pode alterar sua própria língua de sua página de edição de perfil..");
define('LANG_page_auto_refresh', "Atualizar páginas automaticamente");
define('LANG_page_auto_refresh_info', "Page Auto Refresh configurações é usado principalmente na depuração do painel No uso normal, este deve ser definido como Ligado..");
define('LANG_smtp_server', "Servidor de E-Mail de saída");
define('LANG_smtp_server_info', "Este é o servidor de correio de saída (servidor SMTP) que é usado, por exemplo, enviou senhas esquecidas para os usuários, localhost por padrão..");
define('LANG_panel_email_address', "Endereço de email de saída");
define('LANG_panel_email_address_info', "Este é o endereço de e-mail que está no campo de quando as senhas são enviadas para os usuários.");
define('LANG_panel_name', "Nome do Painel, nome que será visível para todos os utilizadores");
define('LANG_panel_name_info', "Nome do painel que é mostrado no título da página. Esse valor irá anular todos os títulos das páginas, se não estiver vazio.");
define('LANG_feed_enable', "Feed Habilitar LGSL");
define('LANG_feed_enable_info', "Se o seu webhost tiver um firewall que esteja bloqueando a porta da consulta, então você precisa abrir a porta manualmente.");
define('LANG_feed_url', "URL do feed");
define('LANG_feed_url_info', "GrayCube.com está compartilhando um feed LGSL na URL:<br><b>http://www.greycube.co.uk/lgsl/feed/lgsl_files/lgsl_feed.php</b>");
define('LANG_charset', "Codificação de Caracteres");
define('LANG_charset_info', "UTF8, ISO, ASCII, etc... Verifica a codificação de caracteres definida em arquivos de idioma. Deixe em branco para usar o idioma padrão.");
define('LANG_steam_user', "Usuário Steam");
define('LANG_steam_user_info', "Um usuário e senha Steam é necessário para efetuar o download de alguns servidores de jogos como o CS:GO.");
define('LANG_steam_pass', "Senha do Steam");
define('LANG_steam_pass_info', "Aqui você define a senha para a conta Steam.");
define('LANG_steam_guard', "Código do Steam Guard");
define('LANG_steam_guard_info', "Alguns usuários poussem o Steam Guard ativado para protegerem suas contas de hackers.<br>O código é enviado para o email da conta quando a primeira autenticação no Steam é feita.");
define('LANG_smtp_port', "Porta SMTP");
define('LANG_smtp_port_info', "Se a porta SMTP não for a padrão (25), insira a porta nesse campo.");
define('LANG_smtp_login', "Usuário SMTP");
define('LANG_smtp_login_info', "Se o servidor SMTP exigir autenticação, insira o usuário nesse campo.");
define('LANG_smtp_passw', "Senha SMTP");
define('LANG_smtp_passw_info', "Se você não definir uma senha, a autenticação SMTP será desativada.");
define('LANG_smtp_secure', "Segurança SMTP");
define('LANG_smtp_secure_info', "Criptografia SSL/TLS para conexões SMTP");
define('LANG_time_zone', "Fuso horário");
define('LANG_time_zone_info', "Define um fuso horário padrão para todas as funções de hora/data.");
define('LANG_query_cache_life', "Status da cache de consulta");
define('LANG_query_cache_life_info', "Define o tempo limite em segundos antes do status do servidor ser atualizado.");
define('LANG_query_num_servers_stop', "Desativar as consultas do servidor de jogos após");
define('LANG_query_num_servers_stop_info', "Use esta configuração para desativar consultas se um usuário possui mais servidores de jogos do que esta quantidade especificada para acelerar o carregamento do painel.");
define('LANG_editable_email', "Endereço de E-mail editável ");
define('LANG_editable_email_info', "Selecione se os usuários podem editar seu endereço de e-mail ou não.");
define('LANG_old_dashboard_behavior', "Comportamento antigo do Dashboard");
define('LANG_old_dashboard_behavior_info', "O painel antigo estava a funcionar de forma mais lenta, mas mostra mais informações do servidor (por exemplo, jogadores e mapas atuais).");
define('LANG_rsync_available', "Servidores de sincronização remota disponíveis");
define('LANG_rsync_available_info', "Selecione a lista de servidores que será exibida na instalação rsync.");
define('LANG_all_available_servers', "Todos os servidores disponíveis ( rsync_sites.list + rsync_sites_local.list )");
define('LANG_only_remote_servers', "Somente servidores remotos ( rsync_sites.list )");
define('LANG_only_local_servers', "Apenas servidores locais ( rsync_sites_local.list )");
define('LANG_header_code', "Código de cabeçalho");
define('LANG_header_code_info', "Aqui você pode escrever seu próprio código de cabeçalho (como código HTML, código embutido etc.) sem editar o layout do tema.");
define('LANG_support_widget_title', "Título do widget de suporte");
define('LANG_support_widget_title_info', "Um título personalizado para o widget de suporte no Dashboard.");
define('LANG_support_widget_content', "Conteúdo do widget de suporte");
define('LANG_support_widget_content_info', "O conteúdo do widget de suporte (código HTML permitido).");
define('LANG_support_widget_link', "Link de widget de suporte");
define('LANG_support_widget_link_info', "O URL do seu site de suporte.");
define('LANG_recaptcha_site_key', "Recaptcha Site Key");
define('LANG_recaptcha_site_key_info', "A chave do site fornecida pelo Google.");
define('LANG_recaptcha_secret_key', "Chave Secreta do Recaptcha");
define('LANG_recaptcha_secret_key_info', "A chave secreta fornecida pelo Google.");
define('LANG_recaptcha_use_login', "Use Recaptcha no Login");
define('LANG_recaptcha_use_login_info', "Se ativado, os usuários terão que resolver o Recaptcha não um robô ao tentar fazer o login.");
define('LANG_login_attempts_before_banned', "Número de tentativas de login falhadas antes do usuário ser banido");
define('LANG_login_attempts_before_banned_info', "Se um usuário tentar iniciar sessão com credenciais inválidas mais do que essas determinadas vezes, o usuário será banido temporariamente pelo painel.");
define('LANG_custom_github_update_username', "Nome de usuário da atualização do GitHub");
define('LANG_custom_github_update_username_info', "Digite seu nome de usuário GitHub SOMENTE para usar seus próprios repositórios bifurcados para atualizar o painel. Isso só deve ser alterado por desenvolvedores que desejam usar seus próprios repositórios para desenvolvimento em vez de verificar possivelmente o código de algum possivel BUG no código principal.");
define('LANG_remote_query', "Consulta remota");
define('LANG_remote_query_info', "Use o servidor remoto (agente) para fazer consultas aos servidores do jogo (Only GameQ and LGSL).");
define('LANG_check_expiry_by', "Verifique a expiração usando");
define('LANG_check_expiry_by_info', "Se configurado para once_logged_in, as atribuições do servidor do jogo do usuário serão excluídas automaticamente após a data de validade. Se configurado para cron_job, você precisará criar uma tarefa cron usando o módulo cron para verificar a data de validade em um intervalo configurado.");
define('LANG_once_logged_in', "Uma vez conectado");
define('LANG_cron_job', "Cron Job");
define('LANG_theme_settings', "Definições de tema");
define('LANG_theme', "Tema");
define('LANG_theme_info', "Tema selecionado aqui será o tema padrão para todos os usuários Os usuários podem alterar seu tema de sua página do perfil..");
define('LANG_welcome_title', "Bem-vindo Título");
define('LANG_welcome_title_info', "Permite o título seja exibido na parte superior do Painel.");
define('LANG_welcome_title_message', "Bem-vindo a Mensagem de título");
define('LANG_welcome_title_message_info', "A mensagem de título que é exibida na parte superior do painel (código HTML permitido).");
define('LANG_logo_link', "Logos Link");
define('LANG_logo_link_info', "The logos hyperlink. <b style='font-size:10px; font-weight:normal;'>(Deixá-lo em branco o vinculará ao Dashboard)</b>");
define('LANG_custom_tab', "Tab personalizado");
define('LANG_custom_tab_info', "Adiciona uma guia personalizável no final do menu. <b style='font-size:10px; font-weight:normal;'>(Aplique e atualize esta página para editar as configurações da aba)</b>");
define('LANG_custom_tab_name', "Nome da guia personalizada");
define('LANG_custom_tab_name_info', "As guias exibem o nome.");
define('LANG_custom_tab_link', "Link de guia personalizado");
define('LANG_custom_tab_link_info', "O hiperlink de guias.");
define('LANG_custom_tab_sub', "Custom Sub-Tabs");
define('LANG_custom_tab_sub_info', "Adiciona sub-guias personalizáveis ​​ao pairar sobre o 'separador personalizado'.");
define('LANG_custom_tab_sub_name', "Sub-Tab #1 Nome");
define('LANG_custom_tab_sub_link', "Sub-Tab #1 Link");
define('LANG_custom_tab_sub_name2', "Sub-Tab #2 Nome");
define('LANG_custom_tab_sub_link2', "Sub-Tab #2 Link");
define('LANG_custom_tab_sub_name3', "Sub-Tab #3 Nome");
define('LANG_custom_tab_sub_link3', "Sub-Tab #3 Link");
define('LANG_custom_tab_sub_name4', "Sub-Tab #4 Nome");
define('LANG_custom_tab_sub_link4', "Sub-Tab #4 Link");
define('LANG_custom_tab_target_blank', "Abas com trajecto personalizado");
define('LANG_custom_tab_target_blank_info', "Define todas as abas alvo. <b style='font-size:10px; font-weight:normal;'>(Self_Page = Abre o link na mesma página. New_Page  =  Abre o link na nova guia.)</b>");
define('LANG_bg_wrapper', "Fundo Completo");
define('LANG_bg_wrapper_info', "A imagem de fundo completo. <b style='font-size:10px; font-weight:normal;'>(Apenas disponível em alguns temas.)</b>");
define('LANG_show_server_id_game_monitor', "Mostrar ID do servidor na página do Monitor de jogos");
define('LANG_show_server_id_game_monitor_info', "Mostre a coluna do ID do servidor do jogo no Game Monitor para fazer correspondência dos arquivos criados pelo agente no servidor do jogo real");
define('LANG_default_game_server_home_path_prefix', "Prefixo do directório inicial do servidor de jogos padrão");
define('LANG_default_game_server_home_path_prefix_info', "Digite um prefixo de caminho para onde você deseja que as casas do servidor do jogo sejam criadas por padrão. Você pode usar \"{USERNAME}\" no caminho que será substituído pelo nome de usuário Do seu Painel, o servidor do jogo está sendo atribuído a.  Você pode usar \"{GAMEKEY}\" no caminho que será substituído por um nome de minúscula.  Você pode usar \"{SKIPID}\" em qualquer lugar no caminho para pular anexando o ID inicial ao caminho.  Exemplo: /ogp/games/{USERNAME}/{GAMEKEY}{SKIPID} irá se tornar /ogp/games/username/arkse/.  Exemplo2:  /ogp/games ira se tornar /ogp/games/2 onde 2 é a identificação dos servidores do jogo.");
define('LANG_use_authorized_hosts', "Limitar a API de hosts autorizados definidos");
define('LANG_use_authorized_hosts_info', "Ative esta configuração para permitir somente chamadas de API de endereços IP predefinidos e aprovados.&nbsp; Os endereços aprovados podem ser definidos nesta página quando a configuração estiver ativada.&nbsp; Se essa configuração estiver desabilitada, um usuário usando uma chave válida terá acesso à API de qualquer endereço IP.&nbsp; Os usuários que usam uma chave válida poderão usar a API para gerenciar qualquer servidor de jogo que eles tenham permissão para administrar.");
define('LANG_setup_api_authorized_hosts', "Define o API das hosts autorizadas");
define('LANG_autohorized_hosts', "Hosts Autorizadas");
define('LANG_add', "Adicionar");
define('LANG_remove', "Remover");
define('LANG_default_trusted_hosts', "Hosts Confiáveis Padrão");
define('LANG_trusted_host_or_proxy_addresses_or_cidr', "Hosts Confiáveis ou Proxies (IPv4/IPv6 endereços ou CIDR*Classless Inter-Domain Routing*)");
define('LANG_trusted_forwarded_ip_addresses_or_cidr', "IPs Confiaveis de Encaminhamentos (IPv4/IPv6 Endereços ou *Classless Inter-Domain Routing*)");
define('LANG_reset_game_server_order', "Reset Game Server Ordering");
define('LANG_reset_game_server_order_info', "Resets game server ordering back to the default of using the server ID");


?>
