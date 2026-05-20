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

define('LANG_manage_listings', "Zarządzaj wiadomościami");
define('LANG_add_new_listing', "Napisz wiadomość");
define('LANG_your_current_listings', "Twoje aktualne wiadomości");
define('LANG_edit', "Edytuj");
define('LANG_date', "Data");
define('LANG_images', "Obrazy");
define('LANG_title', "Tytuł");
define('LANG_description', "Treść wiadomości");
define('LANG_written_by', "Autor");
define('LANG_details', "Czytaj Więcej");
define('LANG_modify', "Modyfikuj");
define('LANG_save', "Zapisz");
define('LANG_delete', "Usuń");
define('LANG_sure_to_delete', "Czy na pewno chcesz usunąć te wiadomości?");
define('LANG_go_back', "Wróć");
define('LANG_new_added_success', "Wiadomość została opublikowana!");
define('LANG_add_another', "Dodaj kolejną");
define('LANG_or_message', "lub");
define('LANG_please_select', "Wybierz");
define('LANG_submit', "Zatwierdź");
define('LANG_edit_listing', "Edytuj wiadomości");
define('LANG_modifications_saved', "Nowe wartości zostały zapisane pomyślnie!");
define('LANG_modify_images', "Modyfikuj zdjęcia nowych artykułów");
define('LANG_upload_more_images', "Wyślij więcej obrazków");
define('LANG_latest_news', "Ostatnia wiadomość");
define('LANG_search_results', "Wyniki Wyszukiwania");
define('LANG_no_results', "Nie znaleziono żadnych wiadomości.");
define('LANG_config_options', "Opcje wiadomości");
define('LANG_date_format', "Format Daty");
define('LANG_results_per_page', "Wiadomości na Stronę");
define('LANG_enable_search', "Włącz wyszukiwarkę");
define('LANG_image_quality', "Jakość obrazka (0-100)");
define('LANG_max_image_width', "Maksymalna szerokość obrazka (px)");
define('LANG_gallery_theme', "Wygląd galerii zdjęć");
define('LANG_images_bottom', "Pozycja galerii zdjęć");
define('LANG_img_bottom', "Pod artykułem");
define('LANG_img_right', "Po prawej stronie artykułu");
define('LANG_no_word', "Nie");
define('LANG_yes_word', "Tak");
define('LANG_no_access', "Nie masz prawa do tej strony. Twoje działanie zostanie zarejestrowane w celu przeprowadzenia dalszej inspekcji.");
define('LANG_write_permission_required', "Wymagane uprawnienia do pisania");
define('LANG_fix_permission', "Proszę poprawić uprawnienia. Moduł nie będzie działał zgodnie z oczekiwaniami, dopóki nie zostaną naprawione żadne uprawnienia.");
define('LANG_check_permissions', "Sprawdź Uprawnienia");
define('LANG_permission_ok', "Wymagane uprawnienia są w porządku!");
define('LANG_empty_title', "Proszę wypełnić tytuł");
define('LANG_empty_description', "Proszę wypełnić treść");
define('LANG_empty_author', "Proszę wypełnić imię i nazwisko autora");
define('LANG_gd_fail', "Rozszerzenie GD nie jest zainstalowane lub włączone na serwerze. Przesyłanie zdjęć jest wyłączone.");
define('LANG_search_news', "Szukaj wiadomości");
define('LANG_help', "pomoc");
define('LANG_help_date', "Uzyskaj pomoc dotyczącą sformatowania daty");
define('LANG_id_invalid', "ID wiadomości nie istnieje");
define('LANG_id_not_set', "ID wiadomości nie jest ustawiony");
define('LANG_unauthorized_access', "Nieautoryzowany dostęp z");
define('LANG_wysiwyg', "Edytor WYSIWYG");
define('LANG_tinymce_lang', "Język Tiny MCE");
define('LANG_da', "Duński");
define('LANG_de', "Niemiecki");
define('LANG_en_GB', "Angielski");
define('LANG_es', "Hiszpański");
define('LANG_fi', "Fiński");
define('LANG_fr_FR', "Francuski");
define('LANG_it', "Włoski");
define('LANG_pl', "Polski");
define('LANG_pt_PT', "Portugalski");
define('LANG_ru', "Rosyjski");
define('LANG_tinymce_skin', "Wygląd Tiny MCE");
define('LANG_tinymce_skin_custom', "Musisz przesłać własną niestandardowy styl<b>modules/news/js/tinymce/skins/custom/</b>folder, aby móc korzystać z tego stylu. Jeśli wybierzesz bez zmian stylu, napotkasz problemy. Utwórz własny styl tutaj<a href='http://skin.tinymce.com/' target='_blank'>http://skin.tinymce.com/</a>.");
define('LANG_safe_HTML', "HTML Purifier");
define('LANG_safe_HTML_en', "HTML Purifier włączony");
define('LANG_safe_HTML_dis', "HTML Purifier wyłączony");
define('LANG_safe_HTML_en_info', "Treść HTML artykułu w szczegółowym widoku zostanie oczyszczona. Spowoduje to usunięcie niektórych znaczników HTML, takich jak iframes. Edytuj plik<b>modules/news/config.php</b>, aby zmienić ustawienie 'safe_HTML' z wartości '1' (włączone) na wartość '0' (wyłączone), aby wyłączyć to zachowanie i umożliwić użycie pełnego HTML bez ograniczeń.");
define('LANG_safe_HTML_dis_info', "Treść HTML artykułu w szczegółowym widoku nie zostanie oczyszczona. Edytuj plik<b>modules/news/config.php</b>by zmienić ustawienie 'safe_HTML' z wartości '0' (wyłączone) na wartość \"1\" (włączona), aby umożliwić bezpieczne używanie tagów HTML.");
?>