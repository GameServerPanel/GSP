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

define('LANG_your_profile', "O seu prefil");
define('LANG_new_password', "Nova senha");
define('LANG_retype_new_password', "Digite novamente a nova senha");
define('LANG_login_name', "Nome de usuário");
define('LANG_language', "Idioma");
define('LANG_first_name', "Nome");
define('LANG_page_limit', "Itens por página");
define('LANG_page_limit_info', "Número de itens exibidos por página. O número de itens não pode ser inferior a 10.");
define('LANG_last_name', "Sobrenome");
define('LANG_phone_number', "Telefone");
define('LANG_email_address', "Endereço de e-mail");
define('LANG_city', "Cidade");
define('LANG_province', "Estado");
define('LANG_country', "País");
define('LANG_comment', "Comentários");
define('LANG_expires', "Expira");
define('LANG_save_profile', "Salvar perfil");
define('LANG_new_password_info', "Quando o campo da senha estiver vazio, a senha não será atualizada");
define('LANG_theme', "Tema");
define('LANG_theme_info', "Tema selecionado aqui será o tema padrão para todos os usuários Os usuários podem alterar seu tema de sua página do perfil..");
define('LANG_expires_info', "Data em que a conta do usuário expira. A conta não é excluída, mas o usuário não pode mais fazer login.");
define('LANG_password_mismatch', "a senha não corresponde");
define('LANG_current_password', "Senha atual");
define('LANG_current_password_info', "A sua senha atual.");
define('LANG_current_password_mismatch', "Sua senha atual não coincide com a da base de dados.");
define('LANG_add_new_user', "Adicionar um novo usuário");
define('LANG_edit_user_groups', "Editar grupos de usuários");
define('LANG_users', "Usuários");
define('LANG_user_role', "Regra de usuário");
define('LANG_full_name', "Nome completo");
define('LANG_edit_games', "Editar Jogos");
define('LANG_edit_profile', "Editar Perfil");
define('LANG_confirm_password', "confirmação de senha");
define('LANG_you_need_to_enter_both_passwords', "Você precisa inserir ambas as senhas.");
define('LANG_passwords_did_not_match', "As senhas não combinaram.");
define('LANG_could_not_add_user_because_user_already_exists', "Não foi possível adicionar usuário, porque o usuário <em>%s</em> já existe.");
define('LANG_successfully_added_user', "Usuário adicionado com sucesso <em>%s</em>.");
define('LANG_add_a_new_user', "Adicionar um novo usuário");
define('LANG_admin', "Administrador");
define('LANG_user', "Usuário");
define('LANG_user_with_id_does_not_exist', "O usuário com ID %s não existe.");
define('LANG_are_you_sure_you_want_to_delete_user', "Tem certeza de que deseja excluir o usuário <em>%s</em>?");
define('LANG_unable_to_delete_user', "Não é possível excluir o usuário %s.");
define('LANG_successfully_deleted_user', "Usuário excluído com sucesso <b>%s</b>.");
define('LANG_failed_to_update_user_profile_error', "Falha ao atualizar o perfil do usuário. Erro: %s");
define('LANG_profile_of_user_modified_successfully', "O perfil do usuário <b>%s</b> foi modificado com sucesso.");
define('LANG_no_subusers', "Nenhum subusuario está disponível para ser atribuído a um grupo. Crie contas de sub-usuário.");
define('LANG_ownedby', "Propriatario Parent");
define('LANG_andSubUsers', "E todos os seus subgerentes?");
define('LANG_subusers', "Subusers");
define('LANG_show_subusers', "Mostrar Subusers");
define('LANG_hide_subusers', "Ocultar Subusers");
define('LANG_info_group', "Desta página é possível determinar grupos de usuários. Você pode atribuir servidores ao grupo para que estejam disponíveis para todos os usuários do grupo.");
define('LANG_add_new_group', "Adicionar novo grupo");
define('LANG_group_name', "Nome do grupo");
define('LANG_add_group', "Adicionar grupo");
define('LANG_no_groups_available', "Não há grupos disponíveis.");
define('LANG_delete_group', "Eliminar grupo");
define('LANG_add_user_to_group', "Adicionar usuário ao grupo");
define('LANG_add_user', "Adicionar usuário");
define('LANG_remove_from_group', "Remover do grupo");
define('LANG_add_server_to_group', "Adicionar servidor ao grupo");
define('LANG_add_server', "Adicionar servidor");
define('LANG_servers_in_group', "Servidores em grupo");
define('LANG_no_servers_in_group', "Nenhum servidor em grupo %s.");
define('LANG_available_groups', "Grupos disponíveis");
define('LANG_assign_homes', "Atribuir diretórios");
define('LANG_successfully_added_group', "Grupo adicionado com sucesso %s.");
define('LANG_group_name_empty', "O nome do grupo não pode estar vazio.");
define('LANG_failed_to_add_group', "Falha ao adicionar grupo %s.");
define('LANG_could_not_add_user_to_group', "Não foi possível adicionar usuário %s ao grupo %s, porque já pertence.");
define('LANG_successfully_added_to_group', ">Adicionado com sucesso %s ao grupo <em>%s</em>.");
define('LANG_could_not_add_server_to_group', "Não foi possível adicionar servidor ao grupo %s, porque já pertence.");
define('LANG_successfully_added_server_to_group', "Servidor adicionado com sucesso ao grupo <em>%s</em>.");
define('LANG_successfully_removed_from_group', "Excedido com sucesso %s do grupo <em>%s</em>.");
define('LANG_could_not_delete_server_from_group', "Não foi possível excluir o servidor %s do grupo <em>%s</em>.");
define('LANG_successfully_removed_server_from_group', "Servidor %s de grupo bem sucedido do grupo <em>%s</em>.");
define('LANG_group_with_id_does_not_exist', "%s grupo não existe.");
define('LANG_are_you_sure_you_want_to_delete_group', "Tem certeza de que deseja excluir o grupo <em>%s</em>?");
define('LANG_unable_to_delete_group', "Não foi possível excluir o grupo %s.");
define('LANG_successfully_deleted_group', "Grupo excluído com sucesso <b>%s</b>.");
define('LANG_editing_profile', "Perfil de edição: %s");
define('LANG_valid_user', "Por favor, especifique um usuário válido.");
define('LANG_enter_valid_username', "Digite um nome de usuário válido.");
define('LANG_unexpected_role', "Função de usuário inesperada recebida.");
define('LANG_search', "Pesquisa");
define('LANG_api_token', "API Token");
define('LANG_user_receives_emails', "Receber e-mails");
?>
