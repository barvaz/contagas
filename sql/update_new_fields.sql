ALTER TABLE `ordini`
ADD `fl_closed` TINYINT NOT NULL DEFAULT '0' AFTER `fl_splitted`;

drop view v_ordini;
CREATE ALGORITHM = UNDEFINED VIEW  `v_ordini` AS SELECT ordini.importo - SUM( movimenti.importo ) AS diff, SUM( movimenti.importo ) AS tot_movimenti, ordini . *
FROM  `ordini` LEFT JOIN movimenti ON movimenti.id_ordine = ordini.id
GROUP BY ordini.id, ordini.`id_fornitore` , ordini.`importo` , ordini.`dt_ordine` , ordini.`id_causale` , ordini.`ds_nota` , ordini.`id_autore` , ordini.`dt_ins` , ordini.`dt_agg`
