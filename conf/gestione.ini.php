
;Copyright 2013 Amit Moravchick amit.moravchick@gmail.com
;
;Licensed under the Apache License, Version 2.0 (the "License");
;you may not use this file except in compliance with the License.
;You may obtain a copy of the License at
;
;   http://www.apache.org/licenses/LICENSE-2.0
;
;Unless required by applicable law or agreed to in writing, software
;distributed under the License is distributed on an "AS IS" BASIS,
;WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
;See the License for the specific language governing permissions and
;limitations under the License.



[users]
table=users
key=id
orderby=nm_cognome
filter="fl_attivo=1"
fields=id,nm_nome,nm_cognome,ds_email,ds_telefono,indirizzo_1,indirizzo_2,username,password,fl_admin,fl_contabile,fl_attivo,dt_ins,dt_agg

id_ro=true

username_type=text
username_label=username
username_required=true

password_type=password
password_label=password

nm_nome_type=text
nm_nome_label=nome
nm_nome_required=true

nm_cognome_type=text
nm_cognome_label=cognome
nm_cognome_required=true

ds_email_type=text
ds_email_label=email
ds_email_required=true

ds_telefono_type=text
ds_telefono_label=telefono
ds_telefono_required=true

indirizzo_1_type=text
indirizzo_1_label=indirizzo
indirizzo_1_required=true

indirizzo_2_type=text
indirizzo_2_label=indirizzo 2

fl_admin_type=checkbox
fl_admin_label=admin
fl_admin_defaultvalue=0

fl_contabile_type=checkbox
fl_contabile_label=contabile
fl_contabile_defaultvalue=0

fl_attivo_type=checkbox
fl_attivo_label=attivo
fl_attivo_defaultvalue=1

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[fornitori]
table=fornitori
key=id
orderby=nm_nome
fields=id,nm_nome,ds_descrizione,ds_email,ds_telefono,indirizzo_1,indirizzo_2,iban,dt_ins,dt_agg

id_ro=true

nm_nome_type=text
nm_nome_label=nome
nm_nome_required=true

iban_type=text
iban_label=iban
iban_required=true

ds_descrizione_type=htmlarea
ds_descrizione_label=descrizione
ds_descrizione_required=true
ds_descrizione_config=simple

ds_email_type=text
ds_email_label=email
ds_email_required=true

ds_telefono_type=text
ds_telefono_label=telefono
ds_telefono_required=true

indirizzo_1_type=text
indirizzo_1_label=indirizzo
indirizzo_1_required=true

indirizzo_2_type=text
indirizzo_2_label=indirizzo 2

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[versamenti]
table=versamenti
key=id
orderby=dt_versamento desc
fields=id,id_gasista,importo,id_causale,ds_nota,id_autore,dt_versamento,dt_ins,dt_agg

id_ro=true

importo_type=text
importo_label=importo
importo_required=true

dt_versamento_type=text
dt_versamento_label=data versamento
dt_versamento_required=true

ds_nota_type=text
ds_nota_label=descrizione
;ds_nota_required=true

id_gasista_type=combo
id_gasista_label=gasista
id_gasista_required=false
id_gasista_combo_source=table
id_gasista_combo_lookup=users
id_gasista_combo_key=id
id_gasista_combo_value=nm_cognome,nm_nome
id_gasista_combo_order=nm_cognome
id_gasista_combo_emptyline=false
id_gasista_combo_filter="fl_attivo=1"
id_gasista_multiple=FALSE

id_causale_type=combo
id_causale_label=causale
id_causale_required=true
id_causale_combo_source=table
id_causale_combo_lookup=causali
id_causale_combo_key=id
id_causale_combo_value=ds_causale
id_causale_combo_order=ds_causale
id_causale_combo_emptyline=TRUE
id_causale_multiple=FALSE

id_autore_type=combo
id_autore_label=autore
;id_autore_required=true
id_autore_ro=true
id_autore_combo_source=table
id_autore_combo_lookup=users
id_autore_combo_key=id
id_autore_combo_value=nm_cognome,nm_nome
id_autore_combo_order=nm_cognome
id_autore_combo_emptyline=TRUE
id_autore_combo_filter="fl_attivo=1"
id_autore_multiple=FALSE

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[ordini]
table=ordini
key=id
orderby="dt_ordine desc"
fields=id,id_fornitore,importo,id_causale,ds_nota,fl_open,fl_paid,fl_splitted,fl_closed,id_autore,dt_ordine,dt_ins,dt_agg

id_ro=true

importo_type=text
importo_label=importo
importo_required=true

dt_ordine_type=text
dt_ordine_label=data ordine
dt_ordine_required=true

ds_nota_type=text
ds_nota_label=descrizione
ds_nota_required=true

fl_open_type=checkbox
fl_open_label=ordine aperto
fl_open_defaultvalue=0

fl_paid_type=checkbox
fl_paid_label=ordine pagato
fl_paid_defaultvalue=0

fl_splitted_type=checkbox
fl_splitted_label=ordine ripartito
fl_splitted_defaultvalue=0

fl_closed_type=checkbox
fl_closed_label=ordine chiuso
fl_closed_defaultvalue=0

id_fornitore_type=combo
id_fornitore_label=fornitore
id_fornitore_required=true
id_fornitore_combo_source=table
id_fornitore_combo_lookup=fornitori
id_fornitore_combo_key=id
id_fornitore_combo_value=nm_nome
id_fornitore_combo_order=nm_nome
id_fornitore_combo_emptyline=TRUE
id_fornitore_multiple=FALSE

id_causale_type=combo
id_causale_label=causale
id_causale_required=true
id_causale_combo_source=table
id_causale_combo_lookup=causali
id_causale_combo_key=id
id_causale_combo_value=ds_causale
id_causale_combo_order=ds_causale
id_causale_combo_emptyline=TRUE
id_causale_multiple=FALSE

id_autore_type=combo
id_autore_label=autore
;id_autore_required=true
id_autore_ro=true
id_autore_combo_source=table
id_autore_combo_lookup=users
id_autore_combo_key=id
id_autore_combo_value=nm_cognome,nm_nome
id_autore_combo_order=nm_cognome
id_autore_combo_emptyline=TRUE
id_autore_combo_filter="fl_attivo=1"
id_autore_multiple=FALSE

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[v_ordini]
table=v_ordini
key=id
orderby="dt_ordine desc"
fields=id,id_fornitore,importo,id_causale,ds_nota,fl_open,fl_paid,fl_splitted,fl_closed,id_autore,dt_ordine,dt_ins,dt_agg,diff,tot_movimenti

id_ro=true

importo_type=text
importo_label=importo
importo_required=true

dt_ordine_type=text
dt_ordine_label=data ordine
dt_ordine_required=true

ds_nota_type=text
ds_nota_label=descrizione
ds_nota_required=true

fl_open_type=checkbox
fl_open_label=ordine aperto
fl_open_defaultvalue=0

fl_paid_type=checkbox
fl_paid_label=ordine pagato
fl_paid_defaultvalue=0

fl_splitted_type=checkbox
fl_splitted_label=ordine ripartito
fl_splitted_defaultvalue=0

fl_closed_type=checkbox
fl_closed_label=ordine chiuso
fl_closed_defaultvalue=0

id_fornitore_type=combo
id_fornitore_label=fornitore
id_fornitore_required=true
id_fornitore_combo_source=table
id_fornitore_combo_lookup=fornitori
id_fornitore_combo_key=id
id_fornitore_combo_value=nm_nome
id_fornitore_combo_order=nm_nome
id_fornitore_combo_emptyline=TRUE
id_fornitore_multiple=FALSE

id_causale_type=combo
id_causale_label=causale
id_causale_required=true
id_causale_combo_source=table
id_causale_combo_lookup=causali
id_causale_combo_key=id
id_causale_combo_value=ds_causale
id_causale_combo_order=ds_causale
id_causale_combo_emptyline=TRUE
id_causale_multiple=FALSE

id_autore_type=combo
id_autore_label=autore
;id_autore_required=true
id_autore_ro=true
id_autore_combo_source=table
id_autore_combo_lookup=users
id_autore_combo_key=id
id_autore_combo_value=nm_cognome,nm_nome
id_autore_combo_order=nm_cognome
id_autore_combo_emptyline=TRUE
id_autore_combo_filter="fl_attivo=1"
id_autore_multiple=FALSE

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[movimenti]
table=movimenti
key=id
orderby=id desc
fields=id,id_gasista,id_ordine,importo,ds_nota,id_autore,dt_ins,dt_agg

id_ro=true

importo_type=text
importo_label=importo
importo_required=true

ds_nota_type=text
ds_nota_label=descrizione
;ds_nota_required=true

id_gasista_type=combo
id_gasista_label=gasista
id_gasista_required=false
id_gasista_combo_source=table
id_gasista_combo_lookup=users
id_gasista_combo_key=id
id_gasista_combo_value=nm_cognome,nm_nome
id_gasista_combo_order=nm_cognome
id_gasista_combo_emptyline=false
id_gasista_combo_filter="fl_attivo=1"
id_gasista_multiple=FALSE

id_ordine_type=combo
id_ordine_label=ordine
id_ordine_required=true
id_ordine_combo_source=table
id_ordine_combo_lookup=ordini
id_ordine_combo_key=id
id_ordine_combo_value=dt_ordine,ds_nota
id_ordine_combo_order=dt_ordine desc,ds_nota
id_ordine_combo_filter="fl_splitted=0"
id_ordine_combo_emptyline=TRUE
id_ordine_multiple=FALSE

id_autore_type=combo
id_autore_label=autore
;id_autore_required=true
id_autore_ro=true
id_autore_combo_source=table
id_autore_combo_lookup=users
id_autore_combo_key=id
id_autore_combo_value=nm_cognome,nm_nome
id_autore_combo_order=nm_cognome
id_autore_combo_emptyline=TRUE
id_autore_combo_filter="fl_attivo=1"
id_autore_multiple=FALSE

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true

[causali]
table=causali
key=id
orderby=id
fields=id,ds_causale,dt_ins,dt_agg

id_ro=true

ds_causale_type=text
ds_causale_label=descrizione
ds_causale_required=true

dt_ins_label=data inserimento
dt_ins_type=text
dt_ins_ro=true

dt_agg_label=data aggiornamento
dt_agg_type=text
dt_agg_ro=true