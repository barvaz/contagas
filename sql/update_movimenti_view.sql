CREATE ALGORITHM = UNDEFINED VIEW  `v_pagamenti` AS SELECT pagamenti.importo - SUM( movimenti.importo ) AS diff, SUM( movimenti.importo ) AS tot_movimenti, pagamenti . *
FROM  `pagamenti` , movimenti
WHERE movimenti.id_pagamento = pagamenti.id
GROUP BY pagamenti.id, pagamenti.`id_fornitore` , pagamenti.`importo` , pagamenti.`dt_pagamento` , pagamenti.`id_causale` , pagamenti.`ds_nota` , pagamenti.`id_autore` , pagamenti.`dt_ins` , pagamenti.`dt_agg`
