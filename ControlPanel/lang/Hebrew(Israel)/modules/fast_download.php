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

define('OGP_LANG_create_alias', "צור כינוי ותיקייה");
define('OGP_LANG_save_as', "שמור כ");
define('OGP_LANG_failure', "שגיאה , כשל ביצירת קובץ הכינוי");
define('OGP_LANG_success', "הושלם");
define('OGP_LANG_fast_download_service_for', "הורדת שירות הפניה מחדש עבור%s");
define('OGP_LANG_to_the_path', "תוביל לתיקייה");
define('OGP_LANG_at_url', "בכתובת");
define('OGP_LANG_create_alias_for', "צור כינוי עבור");
define('OGP_LANG_fast_dl', "הפניית הורדות מחדש (להורדה מהירה)");
define('OGP_LANG_current_aliases_at_remote_server', "כינויים נוכחיים בשרת מרוחק");
define('OGP_LANG_delete_selected_aliases', "מחק כינויים נבחרים");
define('OGP_LANG_no_aliases_defined', "אין עדיין כינויי רשת שהוגדרו על ידי המערכת לשרת מרוחק זה.");
define('OGP_LANG_fastdl_port', "פורט");
define('OGP_LANG_fastdl_port_info', "הפורט שעליו תחל ההורדה המהירה.");
define('OGP_LANG_fastdl_ip', "כתובת");
define('OGP_LANG_fastdl_ip_info', "כתובת IP או הדומיין בו יתחיל שרת ההורדה המהירה שלך, יש לרשום את התחום ב- etc/hosts/ .");
define('OGP_LANG_listing', "מאזין");
define('OGP_LANG_listing_info', "אם 'מופעל', השרת יפרט את תוכן התיקיות.");
define('OGP_LANG_fast_dl_advanced', "הגדרות מתקדמות");
define('OGP_LANG_apply_settings_and_restart_fastdl', "שמור את ההגדרות והתחל מחדש");
define('OGP_LANG_stop_fastdl', "עצור את ההורדה המהירה");
define('OGP_LANG_fast_download_daemon_running', "הורדה המהירה פעילה.");
define('OGP_LANG_fast_download_daemon_not_running', "הורדה המהירה לא פעילה.");
define('OGP_LANG_fastdl_could_not_be_restarted', "לא ניתן היה להפעיל מחדש את ההורדה המהירה");
define('OGP_LANG_configuration_file_could_not_be_written', "אי אפשר לכתוב קובץ זה.");
define('OGP_LANG_remove_folders', "הסר תיקיה לכינויים שנבחרו.");
define('OGP_LANG_remove_folder', "הסר תיקיה");
define('OGP_LANG_delete_alias', "מחק כינוי");
define('OGP_LANG_no_game_homes_assigned', "אין לך שרתים מוקצים לחשבון שלך.");
define('OGP_LANG_select_remote_server', "בחר שרת מרוחק");
define('OGP_LANG_access_rules', "כללי גישה");
define('OGP_LANG_create_aliases', "יצירת כינוי");
define('OGP_LANG_select_game', "בחר משחק");
define('OGP_LANG_games_without_specified_rules', "משחקים ללא כללים שצוינו");
define('OGP_LANG_match_file_extension', "התאם סיומת קבצים");
define('OGP_LANG_match_file_extension_info', "ציין תוספים המופרדים בפסיק,<br> הקבצים התואמים יהיו נגישים. <br><b> השאר ריק לגישה בלתי מוגבלת</b>.");
define('OGP_LANG_match_client_ip', "התאם IP לקוח");
define('OGP_LANG_match_client_ip_info', "ינתנו חיבורים עם IP תואם,<br> ערך ריק יאפשר גישה בלתי מוגבלת. באפשרותך להשתמש<br>במספר כתובות IP או טווחים המופרדים באמצעות פסיק:<br> /xx subnets <br> לדוגמא: 10.0.0.0/16<br> /xxx.xxx.xxx.xxx subnets<br>לדוגמא: 10.0.0.0/255.0.0.0<br>טווחי מקף<br>לדוגמא: 10.0.0.5-230<br>או שימוש בכוכבית ככל ערך<br>לדוגמא:10.0.*.*");
define('OGP_LANG_save_access_rules', "שמור כללי גישה");
define('OGP_LANG_create_access_rules', "צור כללי גישה");
define('OGP_LANG_invalid_entries_found', "נמצאו ערכים לא חוקיים");
define('OGP_LANG_game_name', "שם המשחק");
define('OGP_LANG_alias_already_exists', "הכינוי %s כבר קיים.");
define('OGP_LANG_warning_access_rules_applied_once_alias_created', "אזהרה: כללי הגישה מיושמים כאשר הכינוי נוצר. לא יוחלו שינויים
בכינויים הנוכחיים.");
define('OGP_LANG_autostart_on_agent_startup', "הפעלה אוטומטית בהפעלה של סוכן");
define('OGP_LANG_autostart_on_agent_startup_info', "התחל את הההורדה המהירה באופן אוטומטי כאשר ההשרת יעלה");
define('OGP_LANG_port_forwarded_to_80', "פורט הועבר מ-80");
define('OGP_LANG_port_forwarded_to_80_info', "הפעל אפשרות זו אם היציאה המוגדרת להורדה מהירה הועברה מהפורט 80, כך שהפורט יוסתר בכתובות URL.");
define('OGP_LANG_current_access_rules', "כללי גישה נוכחיים");
?>