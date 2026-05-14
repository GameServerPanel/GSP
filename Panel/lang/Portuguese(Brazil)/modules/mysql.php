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

define('LANG_configured_mysql_hosts', "Anfitrião MySQL configurado");
define('LANG_add_new_mysql_host', "Adicionar host MySQL");
define('LANG_enter_mysql_ip', "Digite o MySQL IP.");
define('LANG_enter_valid_port', "Digite uma porta válida.");
define('LANG_enter_mysql_root_password', "Digite a senha de root do MySQL.");
define('LANG_enter_mysql_name', "Digite o nome do MySQL.");
define('LANG_could_not_add_mysql_server', "Não foi possível adicionar o servidor MySQL.");
define('LANG_game_server_name_info', "O nome do servidor ajuda os usuários a identificar seus servidores.");
define('LANG_note_mysql_host', "Nota: Usando uma \"conexão direta\", o servidor deve aceitar conexões externas para que os servidores possam se conectar remotamente, enquanto a conexão através de um servidor remoto será usada apenas como uma conexão local.");
define('LANG_direct_connection', "Conexão direta");
define('LANG_connection_through_remote_server_named', "Conexão através do servidor remoto chamado %s");
define('LANG_add_mysql_server', "Adicionar servidor MySQL");
define('LANG_mysql_online', "MySQL online");
define('LANG_mysql_offline', "MySQL offline");
define('LANG_encryption_key_mismatch', "Falha na chave de criptografia");
define('LANG_unknown_error', "Erro desconhecido");
define('LANG_remove', "Exclui");
define('LANG_assign_db', "Atribuir banco de dados");
define('LANG_mysql_server_name', "Nome do servidor MySQL");
define('LANG_server_status', "Status do servidor");
define('LANG_mysql_ip_port', "IP MySQL: porta");
define('LANG_mysql_root_passwd', "Senha de root MySQL");
define('LANG_connection_method', "Método de conexão");
define('LANG_user_privilegies', "Privilégios de usuário");
define('LANG_current_dbs', "Bases de dados actuais ");
define('LANG_mysql_name', "Nome do servidor MySQL");
define('LANG_mysql_ip', "IP do MySQL");
define('LANG_mysql_port', "Porta MySQL");
define('LANG_privilegies', "privilégios");
define('LANG_all', "Todos");
define('LANG_custom', "Personalizado");
define('LANG_server_added', "Servidor adicionado.");
define('LANG_sql_alter', "ALTERAR");
define('LANG_sql_create', "CRIAR");
define('LANG_sql_create_temporary_tables', "CRIE TABELAS TEMPORÁRIAS");
define('LANG_sql_drop', "SOLTAR");
define('LANG_sql_index', "ÍNDICE");
define('LANG_sql_insert', "INSERIR");
define('LANG_sql_lock_tables', "TRANCAR TABELAS");
define('LANG_sql_select', "SELECIONAR");
define('LANG_sql_grant_option', "CONCEDER OPÇÃO");
define('LANG_sql_update', "ATUALIZAR");
define('LANG_sql_delete', "APAGAR");
define('LANG_sql_alter_info', "<b>Permite o uso de ALTERAR TABELA.</b>");	
define('LANG_sql_create_info', "<b>Permite o uso de CRIAR TABELA.</b>");	
define('LANG_sql_create_temporary_tables_info', "<b>Permite o uso de CRIAR TEMPORARIAMENTE UMA TABELA.</b>");
define('LANG_sql_delete_info', "<b>Permite o uso de APAGAR.</b>");
define('LANG_sql_drop_info', "<b>Permite o uso de SOLTAR TABELA.</b>");	
define('LANG_sql_index_info', "<b>Permite o uso de CRIAR INDÍCE e SOLTAR INDÍCE.</b>");	
define('LANG_sql_insert_info', "<b>Permite o uso de INSERIR.</b>");	
define('LANG_sql_lock_tables_info', "<b>Permite o uso de TRANCAR TABELAS em tabelas para as quais você tem o privilégio SELECIONAR.</b>");	
define('LANG_sql_select_info', "<b>Permite o uso de SELECIONAR.</b>");
define('LANG_sql_update_info', "<b>Permite o uso de ATUALIZAR.</b>");	
define('LANG_sql_grant_option_info', "<b>Permite que os privilégios sejam concedidos.</b>");
define('LANG_select_game_server', "Selecione o servidor do jogo");
define('LANG_invalid_mysql_server_id', "ID inválido do servidor MySQL.");
define('LANG_there_is_another_db_named_or_user_named', "Existe outro banco de dados chamado  <b>%s</b> ou outro usuário chamado <b>%s</b>.");
define('LANG_db_added_for_home_id', "Adicionado banco de dados para identificação do directorio <b>%s</b>.");
define('LANG_could_not_remove_db', "O banco de dados selecionado não pôde ser removido.");
define('LANG_db_removed_successfully_from_mysql_server_named', "O banco de dados foi removido do servidor %s.");
define('LANG_areyousure_remove_mysql_server', "Tem certeza de que deseja remover o servidor MySQL chamado <b>%s</b>?");
define('LANG_db_changed_successfully', "O banco de dados chamado %s foi alterado com sucesso.");
define('LANG_error_while_remove', "Erro ao remover.");
define('LANG_mysql_server_removed', "O servidor MySQL chamado <b>%s</ b> foi removido com sucesso.");
define('LANG_unable_to_set_changes_to', "Não foi possível configurar as alterações no servidor MySQL chamado <b>%s</b>.");
define('LANG_mysql_server_settings_changed', "O servidor MySQL chamado <b>%s</ b> foi alterado com sucesso.");
define('LANG_editing_mysql_server', "Editar o servidor MySQL chamado <b>%s</b>.");
define('LANG_save_settings', "Guardar defenições");
define('LANG_mysql_dbs_for', "Bancos de dados para o servidor %s");
define('LANG_edit_dbs', "Editar bancos de dados");
define('LANG_edit_db_settings', "Editar configurações do banco de dados");
define('LANG_remove_db', "Remover banco de dados");
define('LANG_save_db_changes', "Salve as alterações no banco de dados.");
define('LANG_add_db', "Adicionar banco de dados");
define('LANG_select_db', "Selecione o banco de dados");
define('LANG_db_user', "Usuário DB");
define('LANG_db_passwd', "Senha de banco de dados");
define('LANG_db_name', "Nome do banco de dados");
define('LANG_enabled', "Ativado");
define('LANG_game_server', "Servidor do jogo");
define('LANG_there_are_no_databases_assigned_for', "Não há bases de dados atribuídas para <b>%s</b>.");
define('LANG_unable_to_connect_to_mysql_server_as', "Não foi possível conectar-se ao servidor MySQL como %s.");
define('LANG_unable_to_create_db', "Não é possível criar banco de dados.");
define('LANG_unable_to_select_db', "Não foi possível selecionar o banco de dados %s.");
define('LANG_db_info', "Informações sobre o banco de dados");
define('LANG_db_tables', "Tabelas de banco de dados");
define('LANG_db_backup', "Backup de banco de dados");
define('LANG_download_db_backup', "Fazer Download do backup de banco de dados");
define('LANG_restore_db_backup', "Restaurar backup de banco de dados");
define('LANG_sql_file', "arquivo(.sql)");
?>