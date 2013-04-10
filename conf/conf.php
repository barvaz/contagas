<?
/*
   Copyright 2013 Amit Moravchick amit.moravchick@gmail.com

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

header ("cache-control: no-cache, must-revalidate");
header ("pragma: no-cache");

error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", TRUE);

// numero risultati per pagina
define("PAGE_SIZE", 20);

define ("USERNAME_CASE_SESITIVE", true);
// al momento la crittografia della password non è implementata
define ("USERPASSWORD_CRYPT", false);

define("DEBUG_ENABLE", false);

define("QUOTA_ANNUALE", 10);

// parametri per il DB MySQL
define ("DB_HOST","localhost");
define ("DB_USER","root");
define ("DB_PWD","mysql");
define ("DB_NAME","gasdelsole");

// parametri per la spedizione delle email
// funziona bene con GMail

// l'indirizzo visibile come mittente (per gmail deve essere l'account)
define ("EMAIL_SENDER", "EMAIL@SERVER.COM");
// l'indirizzo per le risposte)
define ("EMAIL_REPLY_TO", "EMAIL@SERVER.COM");
// indirizzo email da mettere in copia conoscenza
define ("EMAIL_CC", "EMAIL@SERVER.COM");
// nome mittente
define ("EMAIL_SENDER_NAME", "iGas");
// nome per la risposta
define ("EMAIL_REPLY_TO_NAME", "iGas");
// nome utente per il server di mail (l'account google per GMail)
define ("EMAIL_SENDER_USER", "EMAIL@SERVER.COM");
// password per il server di mail
define ("EMAIL_SENDER_PWD", "PASSWORD");


define("SESSIONTIMEOUT", 900);

define("ENABLE_CLEANUP", FALSE);

set_time_limit (300);

define ("WINDOWS_PARAM", "width=1000,height=700,toolbar=no,location=yes,status=yes,scrollbars=yes,resizable=yes");

define("ERROR_LOG_DIR", "../log");

setlocale(LC_TIME, 'it_IT');
date_default_timezone_set ('Europe/Rome');
?>
