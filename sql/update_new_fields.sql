ALTER TABLE `pagamenti`
ADD `fl_open` TINYINT NOT NULL DEFAULT '0' AFTER `ds_nota`,
ADD `fl_paid` TINYINT NOT NULL DEFAULT '0' AFTER `fl_open`,
ADD `fl_splitted` TINYINT NOT NULL DEFAULT '0' AFTER `fl_paid`;
update pagamenti set fl_open = 0, fl_paid = 1, fl_splitted = 1;

drop view v_pagamenti;
CREATE ALGORITHM = UNDEFINED VIEW  `v_pagamenti` AS SELECT pagamenti.importo - SUM( movimenti.importo ) AS diff, SUM( movimenti.importo ) AS tot_movimenti, pagamenti . *
FROM  `pagamenti` LEFT JOIN movimenti ON movimenti.id_pagamento = pagamenti.id
GROUP BY pagamenti.id, pagamenti.`id_fornitore` , pagamenti.`importo` , pagamenti.`dt_pagamento` , pagamenti.`id_causale` , pagamenti.`ds_nota` , pagamenti.`id_autore` , pagamenti.`dt_ins` , pagamenti.`dt_agg`
