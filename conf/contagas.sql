-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 21, 2013 at 11:57 PM
-- Server version: 5.5.28-log
-- PHP Version: 5.3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `contagas`
--

-- --------------------------------------------------------

--
-- Table structure for table `causali`
--

CREATE TABLE IF NOT EXISTS `causali` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ds_causale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `causali`
--

INSERT INTO `causali` (`id`, `ds_causale`, `dt_ins`, `dt_agg`) VALUES
(1, 'Bonifico a fornitore', '2010-12-10 15:49:51', '2011-06-13 10:51:22'),
(2, 'Quota annuale', '2010-12-10 15:50:04', '2010-12-10 15:50:04'),
(3, 'Versamento gasista', '2010-12-10 15:50:17', '2010-12-10 18:09:39'),
(4, 'Spese conto', '2010-12-10 15:50:36', '2010-12-10 18:05:12'),
(7, 'interessi C/C', '2011-08-29 22:38:07', '2011-08-29 22:38:07'),
(6, 'spese extra GAS', '2010-12-10 18:29:00', '2011-08-29 22:37:52'),
(8, 'entrate extra GAS', '2011-08-29 22:38:31', '2011-08-29 22:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `fornitori`
--

CREATE TABLE IF NOT EXISTS `fornitori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nm_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ds_descrizione` text COLLATE utf8_unicode_ci NOT NULL,
  `iban` varchar(27) COLLATE utf8_unicode_ci NOT NULL,
  `ds_email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `ds_telefono` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;

--
-- Dumping data for table `fornitori`
--

INSERT INTO `fornitori` (`id`, `nm_nome`, `ds_descrizione`, `iban`, `ds_email`, `ds_telefono`, `indirizzo_1`, `indirizzo_2`, `dt_ins`, `dt_agg`) VALUES
(1, 'Azienda Agricola fratelli PONZIN', '<p>Allevamento di bovini e suini, trasformazione di latticini e insaccati.<br />Servizi: vendita diretta, agriturismo didattico.<br />Prodotti: latte, formaggi freschi e stagionati, mozzarelle, ricotte, caprini, salumi, pancette.</p>', '-', 'a.ponzin@tiscali.it', '031772005', 'Via Garibaldi 56, 22070 Bregnano (Co)', '', '2010-12-10 14:39:50', '2011-09-13 19:06:25'),
(2, 'Giuseppe Scarabelli-Azienda Agricola il Gelso', '<p>verdura</p><p>&nbsp;</p>', '-', 'info@azilgelso.it', '3407772992-3389219171', 'Via Matteotti, 5  20050 Mezzago (MI)', '', '2011-01-15 16:46:57', '2011-09-12 23:54:35'),
(3, 'Santa Brera', '<p>Salumi. uova e altro</p><p>Salumi. uova e altro</p>', '-', '-', '-', '-', '', '2011-08-29 22:28:06', '2011-09-13 00:02:05'),
(4, 'Spese GAS', '<p>&nbsp;</p><p>uscite dalla cassa: feste o altre iniziative, spese impreviste a fornitore etc...</p>', '-', '-', '-', '-', '', '2011-08-29 22:29:45', '2011-09-13 00:02:23'),
(5, 'Francesca Cerletti', '<p>&nbsp;</p><p>marmellate di sua produzione</p>', '-', 'fran.marmel@yahoo.it', '349 4734754', '-', '', '2011-09-04 19:38:01', '2011-09-04 19:38:01'),
(6, 'Roberto Bucci', '<p>&nbsp;</p><p>Frutta fresca dalla Romagna</p><p>pagamenti a PAGANI DANIELA?</p>', 'IT15C0306909505100000000027', '-', '-', '-', '', '2011-09-04 21:41:18', '2011-09-13 18:52:15'),
(7, 'Avicenna Bio', '<p>&nbsp;</p><p>&nbsp;&nbsp; frutta e verdura</p>', '-', '-', '-', '-', '', '2011-09-04 21:42:35', '2011-09-04 21:42:35'),
(8, 'Corleonesi a milano', '<p>&nbsp;</p><p>prodotti vari da <span>gruppo di sostegno alla Cooperativa Lavoro e Non Solo che lavora i terreni confiscati alla mafia, e non solo...</span></p><p><br />nostro contatto Leonardo La Rocca, bonifici tramite conto su Banca Popolare Etica di Arci Milano</p>', '-', '-', '-', '-', '', '2011-09-04 22:41:00', '2011-09-04 22:41:00'),
(10, 'WELEDA Italia Srl', '<p>&nbsp;</p><p>&nbsp;prodotti per la cura della persona</p>', 'IT59P0306909563000002134186', 'mobizzo@weleda.it', '024877051', 'via del Ticino 6 20153 Milano', '', '2011-09-06 00:15:53', '2011-09-12 23:48:02'),
(11, 'MONTE TESOBO', '<p>AZIENDA AGRICOLA DAL FIOR STEFANIA ?</p><p>&nbsp; tisane piante officinali da agricoltura biologica</p>', 'IT05J0820635350000000005217', 'info@montetesobo.it', '0461-773185', 'Loc.Tesobbo-Roncegno (TN)', '', '2011-09-06 00:17:05', '2011-10-26 18:28:18'),
(12, 'caffe zapatista', '<p>&nbsp; caff&egrave; commercializzato dall''associazione ya basta!</p>', 'IT45F050180160000000010836', 'info@caffezapatista.it', '3332090787', 'c/o casa loca, viale sarca 183 Milano', '', '2011-09-06 00:18:31', '2011-09-12 23:49:57'),
(13, 'pagamento in contanti', '<p>&nbsp;</p><p>&nbsp;fornitore fittizio per far quadrare i conti dei produttori che vengono pagati in contanti</p>', '-', '-', '-', '-', '', '2011-09-06 19:48:34', '2011-09-06 19:48:34'),
(14, 'ROB del bosco scuro', '<p>&nbsp;</p><p>Andrea Rasi, Rob, produce frutta e trasformati</p>', 'IT30L0501811200000000130367', 'info@robdelbosco.com', '3356356572', 'via del Bosco Scuro | 10123 Cavriana (Mantova)', '', '2011-09-12 23:52:29', '2011-09-17 12:17:48'),
(15, 'AZIENDA AGRICOLA DOMER-CONTRADA NOCI', '<p>&nbsp;</p><p>Azienda biologica a conduzione familiare che produce esclusivamente olio extra vergine di oliva spremuto a freddo e olive</p>', 'IT94I0102083690000300325108', 'contradanoci@hotmail.com', 'Andreas Domer 335 8384585', 'Contrada Noci - Casella Aperta - I 94010 AIDONE (EN)', '', '2011-09-12 23:57:47', '2011-09-12 23:57:47'),
(16, 'Società Agricola Pederzani', '<p>&nbsp;</p><p>L ''azienda Agricola Pederzani si trova nella frazione di Pieve di Cusignano a una decina di Km. da Fidenza nella alta valle del Torrente Parola, in un tipico paesaggio collinare.<br />E un ''azienda a conduzione famigliare, riconosciuta biologica nel 1998 dall''Istituto Mediterraneo di Certificazione (ICM).<br />La nostra coltivazione principale &egrave; il frumento seguito da farro e granturco. Le sementi vengono selezionate in azienda, le variet&agrave; sono scelte in base alla loro resistenza alle malattie e adattamento al territorio perch&eacute; non potendo utilizzare fertilizzanti e diserbanti chimici la resa in campo sarebbe sensibilmente compromessa. La granella di cereali viene quindi ( dopo le necessarie operazioni di pulizia) macinata nel mulino aziendale per essere poi confezionata.<br />La macinatura avviene tramite mulino a pietra senza umidificazione della granella, asportazione del germe e altre lavorazioni tipiche dell'' industria. \
nOtteniamo cosi farine di grano tenero, di grano duro antico, di farro, di granturco e anche una piccola produzione di farro semiperlato.<br />In questo modo si attua una filiera corta completa del prodotto: coltivazione, trasformazione, confezionamento e vendita direttamente in azienda o tramite i negozi interessati.</p>', 'IT69I0705865840000000035929', 'info@biopederzani.it', '052462224-Fabio3343843716', 'Fraz. Pieve di Cusignano 9-Fidenza(Parma)', '', '2011-09-13 00:00:32', '2011-09-13 00:00:32'),
(17, 'Bagaggera', '<p>&nbsp;</p><p>&nbsp;formaggi di capra</p><p>&nbsp;</p>', '-', '-', '-', '-', '', '2011-09-27 22:45:28', '2011-09-27 22:45:28'),
(18, 'Giuliana Piccolo', '<p>&nbsp;</p><p>progetto verdure invernali - Cascina Santa Brera</p><p>&nbsp;</p>', 'IT70P0501801600000000105535', '-', '-', '-', '', '2011-10-03 23:17:20', '2011-10-03 23:17:20'),
(19, 'CIAO LATTE', '<p>&nbsp;</p><p>parmigiano</p>', 'IT77P0623065840000035839801', '-', '-', '-', '', '2011-10-07 11:11:49', '2011-10-07 11:11:49'),
(20, 'Valli Unite', '<p>Societ&agrave; Cooperativa Agricola<br />Produzione di vino, carne, salumi, formaggi e farine biologici.<br />Ristorazione ed ospitalit&agrave;</p>', 'IT88W0690648750000000077027', 'gas@valliunite.com', '0131 838100', 'Cascina Montesoro, 2 Fraz. Montale Celli - 15050 Costa Vescovato (AL)', '', '2011-10-11 18:19:16', '2011-10-11 18:19:16'),
(21, 'ALTROMERCATO', '<p>&nbsp;</p><p>CHICO MENDES ONLUS</p><p>prodotti ALTROMERCATO</p>', 'IT65T0504801631000000001373', 'lima@chicomendes.com', '-', '-', '', '2011-10-22 10:05:44', '2011-10-22 10:05:44'),
(22, 'Apicoltura Nomade TERRE ALTE', '<p>&nbsp;</p><p>&nbsp;</p><p>miele!!</p>', '-', '-', '-', '-', '', '2011-10-26 18:08:18', '2011-10-26 18:08:18'),
(23, 'Astorflex', '<p>Scarpe&nbsp;</p>', 'IT02D0200857540000500084647', 'astorflex@astorflex.191.it', '0376 660170', 'ia dell’Industria 9 – 46033 Castel d’Ario (MN)', '', '2011-10-27 22:51:24', '2011-10-27 22:51:24'),
(24, 'Pesce con Intergas', '<p>&nbsp;</p><p><span style="font-size: 13.5pt; color: navy;">Il Tramaglino" Gruppo Paritetico Cooperativo&nbsp;</span><span style="font-size: 13.5pt; color: navy;"> <strong><span><br /></span></strong></span></p>', 'IT74H0103072302000001115625', '-', '-', 'Via Roma 99 58022 Follonica  GR', '', '2011-11-01 18:43:54', '2011-11-01 18:43:54'),
(25, 'caffè Malatesta', '<p>&nbsp;</p><p>&nbsp;Arienti ??</p>', '-', '-', '-', '-', '', '2011-11-02 23:24:05', '2011-11-02 23:24:05'),
(26, 'Hierba Buena', '<p>&nbsp;</p><p>&nbsp; detersivi</p>', '-', '-', '-', '-', '', '2011-11-13 15:56:41', '2011-11-13 15:56:41'),
(27, 'Tomasoni', '<p>formaggi</p>', '-', '-', '-', '-', '', '2011-11-22 10:27:22', '2011-11-22 10:27:22'),
(28, 'SICILIA VOSTRA', '<p class="MsoNormal">&nbsp;</p><p class="MsoNormal">&nbsp;ARANCE, LIMONI, CARCIOFI.....</p><p class="MsoNormal"><span style="text-decoration: underline;"><span style="font-size: 10pt; font-family: &quot;Arial&quot;,&quot;sans-serif&quot;; color: black;">Credito Siciliano, ag. Partinico; intestato NOE Coop Soc<br /></span></span></p>', 'IT82Y0301943490000008031093', '-', '-', '-', '', '2011-12-11 10:11:41', '2011-12-11 10:11:41'),
(29, 'CANEDO', '<p>carne</p>', '-', '-', '-', '-', '', '2011-12-17 00:16:13', '2011-12-17 00:16:13'),
(30, 'cascina NIBAI', '<p>&nbsp;</p><p>varie</p><p>&nbsp;</p><p>&nbsp;</p>', '-', '-', '-', '-', '', '2011-12-23 14:16:18', '2011-12-23 14:16:18'),
(31, 'perlage', '<p>&nbsp;prosecco e altri vini??</p>', '-', '-', '-', '-', '', '2012-01-25 22:30:40', '2012-01-25 22:33:00'),
(32, 'LESCA', '<p>&nbsp;</p><p>riso Lesca</p>', '-', '-', '-', '-', '', '2012-01-31 09:14:29', '2012-01-31 09:14:29'),
(33, 'Moon Cup', '<p>moon cup</p>', '-', '-', '-', '-', '', '2012-01-31 22:17:25', '2012-01-31 22:17:25'),
(34, 'ALCE NERO', '<p>pasta</p>', '-', '-', '-', '-', '', '2012-02-20 12:32:41', '2012-02-20 12:32:41'),
(35, 'MAZZUCONI', '<p>SALDI S. BIAGIO??</p>', '-', '-', '-', '-', '', '2012-02-20 12:40:09', '2012-02-20 12:40:09'),
(36, 'CASCINA RESTA', '<p>&nbsp;</p><p>fragole e non solo....</p>', '-', '-', '-', '-', '', '2012-06-09 22:42:52', '2012-06-09 22:42:52'),
(37, 'Forever living products - Sabine Bourgeau', '<p><span>&nbsp;</span> Gel d''aloe stabilizzato a freddo e prodotti a base di aloe per la salute, la bellezza e l''igiene.</p>', 'Paga Ale Girola direttament', 'sabinemarie.bourgeau@fastwebnet.it', '3297649279', 'on line', '', '2012-06-23 15:12:27', '2012-06-23 15:12:27'),
(38, 'TEA NATURA', '<p>&nbsp;</p><p>prodotti vari</p><p>&nbsp;</p>', '-', '-', '-', '-', '', '2012-07-22 22:29:10', '2012-07-22 22:29:10'),
(39, '5cascine', '<p>verdure invernali</p>', '-', 'giuliana.piccolo@gmail.com', '-', '-', '', '2012-09-21 15:28:01', '2012-09-21 15:29:43'),
(40, 'Birrificio Vecchia Orsa-Cooperativa Sociale FattoriAbilità', '<p>Il birrificio si chiama Vecchia orsa, si trova a Crevalcore (BO) e impiega lavoratori svantaggiati. Gli ingredienti sono certificati e no OGM.<br />Per saperne di pi&ugrave; visitate il loro bel sito www.fattoriabilita.it.<br />Qui sotto l''email con le ultime vicende, a questo link invece l''articolo che me l''ha fatto conoscere : http://tinyurl.com/cucvnjf</p>', 'IT 57 P 02008 66695 0000405', 'vecchiaorsa@fattoriabilita.it', '335 58 00 255', 'V. degli Orsi 692', '40014 Crevalcore (BO)', '2012-09-27 22:02:22', '2012-09-27 22:02:22'),
(41, 'Birrificio Artigianale Gedeone', '<p>&nbsp;</p><p>birra artigianale</p><p>&nbsp;</p>', 'IT97I0690648750000000002428', 'birrragedeone@libero.it', '-', '-', '', '2012-11-08 21:37:22', '2012-11-08 21:37:22'),
(42, 'Armonia e Bontà', '<p>&nbsp;</p><p>Tofu, seitan e affini</p>', '-', '-', '-', '-', '', '2012-11-13 23:17:27', '2012-11-13 23:17:27'),
(43, 'AZIENDA AGRICOLA LE FRATTE DI FRANCESCO TORREGIANI', '<h3>SCHEDA PRODUTTORI DI INTERGAS</h3><p>&nbsp;</p><p><strong>Data di approvazione</strong>: 28/08/2012</p><p>&nbsp;</p><h3>DATI GENERALI</h3><p style="text-align: justify;"><strong>Nome azienda:&nbsp;</strong>Azienda Agricola Biologica "Le Fratte"</p><p style="text-align: justify;"><strong>Indirizzo azienda:&nbsp;</strong>Loc Le Fratte - Monticello Amiata - Comune di Cinigiano</p><p style="text-align: justify;"><strong>Sigla provincia azienda:&nbsp;</strong>GR</p><p style="text-align: justify;"><strong>Telefono:&nbsp;</strong>320 3476257</p><p style="text-align: justify;"><strong>Cellulare:&nbsp;</strong>Loc Le Fratte - Monticello Amiata - Comune di Cinigiano</p><p style="text-align: justify;"><strong>Email:&nbsp;</strong>fratellofiore@gmail.com</p><p style="text-align: justify;"><strong>Breve storia dell''azienda, motivazioni della scelta produttiva e profilo aziendale:&nbsp;</strong>Azienda antichissima estesa per 30 ettari boscati alle pendici
dell''Amiata, di cui 22 ettari di Castagneto da frutto, Biologico.<br />Cultivar presenti: Marrone, Cecio, Bastarda Rossa, e la pi&ugrave; antica la Castagna Domestica.<br />Francesco ha acquistato l''azienda circa 4 anni fa sobbarcandosi di un mutuo consistente da restituire in 30 anni. Le difficolt&agrave; economiche affrontate e insieme l''amore per questo luogo e per la propria attivit&agrave; hanno portato a lanciare un appello alle famiglie che desiderano cambiar vita e che, acquistando fabbricati e terreni nell''azienda stessa,potrebbero condividere questa esperienza con Francesco e la sua famiglia.</p><p style="text-align: justify;"><strong>Eventuali attivit&agrave; sociali, di tutela ambientale, di sostegno a persone svantaggiate, iniziative politiche, culturali, etc:&nbsp;</strong>Associazione per la valorizzazione della Castagna del Monte Amiata, che promuove la conoscenza del prodotto e del territorio attraverso iniziative culturali e gastronomiche</p><p style="text-align: justify;"
><strong>Elenco di alcuni dei GAS riforniti:&nbsp;</strong>Abbiamo partecipato con l''associazione La Castagna del Monte Amiata a Fa la Cosa Giusta 2011 ospiti dello stand di Intergas e fatto un primo ordine per i gas della rete.</p><p style="text-align: justify;"><strong>Associazioni e/o consorzi di appartenenza:&nbsp;</strong>Associazione Castagna del Monte Amiata - http://www.castagna-amiata.it/</p><p style="text-align: justify;"><strong>Coltivatore diretto:&nbsp;</strong>Si</p><p style="text-align: justify;"><strong>Periodo di reperibilit&agrave; prodotti:&nbsp;</strong>Le castegne fresche sono reperibili dal 1/10/ al 31/12.Per ordini successivi al 10/11 il prodotto subisce un trattamento di conservazione immergendolo in acqua per circa 10 giorni&hellip;</p><p style="text-align: justify;"><strong>Quantitativo presunto prodotti:&nbsp;</strong>media annuale 300 q.li raccolti, ma c''&egrave; sempre uno scarto<br />con gli scarti si alimentano i maiali della porcilaia</p><p style="text-align: justify;"
><strong>Agriturismo aperto al pubblico:&nbsp;</strong>No</p><p style="text-align: justify;"><strong>Altre attivit&agrave; e servizi aperti al pubblico:&nbsp;</strong>Si</p><p style="text-align: justify;"><strong>Note:&nbsp;</strong>Visite guidate nel castagneto per scolaresche e studenti, anche con la raccolta delle castagne da parte degli stessi.</p><p>&nbsp;</p><h3>DATI AZIENDALI</h3><p style="text-align: justify;"><strong>Estensione totale azienda (in ettari):&nbsp;</strong>30 ha circa</p><p style="text-align: justify;"><strong>Terreno di propriet&agrave;:&nbsp;</strong>Si</p><p style="text-align: justify;"><strong>Terreno in affitto:&nbsp;</strong>No</p><p style="text-align: justify;"><strong>Estensione bio:&nbsp;</strong>22 Ha circa produce con sistemi naturali compresa la raccolta che viene effettuata manualmente</p><p style="text-align: justify;"><strong>Estensione per coltura:&nbsp;</strong>22 ha castagneto specializzato IGP<br />7 ha bosco<br />il resto fabbricati - abitazioni e porcilaia</p><p
style="text-align: justify;"><strong>Numero collaboratori familiari:&nbsp;</strong>2</p><p style="text-align: justify;"><strong>Numero collaboratori (esclusi i familiari):&nbsp;</strong>Vengono assunti operai a t. deteminato per la pulizia del castagneto&hellip; Per la raccolta vengono assunti operai a T. determinato secondo le necessit&agrave; del periodo</p><p style="text-align: justify;"><strong>Tipo di contratto dei collaboratori:&nbsp;</strong>T. Determinato per due/tre mesi con contratto agricolo avventizio</p><p style="text-align: justify;"><strong>Numero di collaboratori che dimorano in azienda (esclusi i familiari):&nbsp;</strong>nessuno</p><p>&nbsp;</p><h3>ASPETTI ECONOMICI</h3><p style="text-align: justify;"><strong>Fatturato ultimi tre anni:&nbsp;</strong>25000 (2008)</p><p style="text-align: justify;"><strong>Fatturato ultimi tre anni:&nbsp;</strong>30000 (2009)</p><p style="text-align: justify;"><strong>Fatturato ultimi tre anni:&nbsp;</strong>25000(2010)</p><p style="text-align: justify;"
><strong>Minimo quantitativo per trasporto:&nbsp;</strong>kg. 500<br />chiamare il produttore verso il 15 settembre<br />per contatto stagionale e gestire il flusso del fresco</p><p style="text-align: justify;"><strong>Prezzo di vendita dei prodotti principali (IVA compresa):&nbsp;</strong>I prezzi seguono andamenti stagionali (scarsit&agrave; del prodotto, prodotto con qualche difetto dovuto alla scarsit&agrave; di acqua od alla eccessiva siccit&agrave;)<br />per il prodotto sciolto:<br />bastarda rossa 3 &euro; al kg.<br />marroni 4,50 &euro; al kg.<br />per confezioni in sacchetti da 3 kg.:<br />bastrda rossa 4 &euro;<br />marroni 5,50 &euro;</p><p style="text-align: justify;"><strong>Listino dedicato ai GAS:&nbsp;</strong>Si</p><p style="text-align: justify;"><strong>Disponibilit&agrave; ad elaborare analisi dei costi con InterGAS:&nbsp;</strong>Si</p><p style="text-align: justify;"><strong>Finanziamenti e/o sovvenzioni ricevuti:&nbsp;</strong>il finanziamento per la potatura e acquisto macchinari da
parte della Piano di Sviluppo Rurale della Comunit&agrave; Europea</p><p>&nbsp;</p><h3>PRODOTTI</h3><p><strong>Tipi di prodotto:</strong></p><p>- castagne e/o frutta secca</p><p style="text-align: justify;"><strong>Prodotti propri:&nbsp;</strong>Marrone, Cecio, Bastarda Rossa, Castagna Domestica (la pi&ugrave; antica)</p><p style="text-align: justify;"><strong>Prodotti trasformati:&nbsp;</strong>Vengono commissionate all''esterno prioduzione di farina di castagne che viene trasformata da terzi (vedi modalit&agrave; di traspormazione)<br /><br /></p><p style="text-align: justify;"><strong>Vendita di prodotti realizzati da terzi:&nbsp;</strong>no</p><p style="text-align: justify;"><strong>Prodotti realizzati per terzi:&nbsp;</strong>no</p><p>&nbsp;</p><h3>MODALIT&Agrave; DI VENDITA</h3><p style="text-align: justify;"><strong>Punto di vendita diretta:&nbsp;</strong>il castagneto - Azienda Agricola Biologica "Le Fratte"</p><p style="text-align: justify;"><strong>Indirizzo:&nbsp;</strong>Loc Le Fratte -
Monticello Amiata - Comune di Cinigiano</p><p style="text-align: justify;"><strong>Telefono:&nbsp;</strong>320 3476257</p><p style="text-align: justify;"><strong>Email:&nbsp;</strong>fratellofiore@gmail.com</p><p style="text-align: justify;"><strong>Orario punto vendita:&nbsp;</strong>tutto il d&igrave;</p><p style="text-align: justify;"><strong>Vendita diretta dei propri prodotti presso mercati biologici:&nbsp;</strong>Feste della castagna in qualche paese della zona</p><p style="text-align: justify;"><strong>Prodotti e quantit&agrave; venduti direttamente al consumatore finale:&nbsp;</strong>30%, di cui una minima quantit&agrave; ai gas, siamo ancora all''inizio</p><p style="text-align: justify;"><strong>Prodotti e quantit&agrave; non venduti direttamente al consumatore finale:&nbsp;</strong>40% al deposito Pasqui Marzio e altri grossisti e distributori<br />30% negozi della zona</p><p style="text-align: justify;"><strong>Punti di rivendita dei prodotti aziendali dove sia indicato che provengono dalla
vostra azienda:&nbsp;</strong>negozi:<br />"Senafrutta"- Siena<br />Collevaldelsa<br />Certaldo<br />Pistoia</p><p>&nbsp;</p><h3>MODALIT&Agrave; DI COLTIVAZIONE</h3><p style="text-align: justify;"><strong>Conduzione biologica:&nbsp;</strong>Totale</p><p style="text-align: justify;"><strong>Stato certificazione biologica:&nbsp;</strong>Autocertificazione</p><p style="text-align: justify;"><strong>Anno di ottenimento certificazione:&nbsp;</strong>2000</p><p style="text-align: justify;"><strong>Enti certificatori e numero identificativo dei certificati:&nbsp;</strong>Certificazione IGP. Reg.CE n.1904 del 7/9/2000 UE n. 1108 del 30.11.2010 -&nbsp;<br />GUCE L.228 del 8/9/2000 - GUUE L.314 del 10/12/2010</p><p style="text-align: justify;"><strong>Motivo della scelta e note:&nbsp;</strong>l''IGP ha un disciplinare molto rigido e sottoposto a fitti controlli<br />Estratto del disciplinare:<br />"I sesti di impianto, le forme di allevamento, i sistemi di potatura periodica e pluriennale, devono essere quelli in uso
tradizionale e generalizzato nella zona amiatina o, comunque, atti a non modificare le caratteristiche di tipicit&agrave; dei frutti. La densit&amp; agrave; di piante ad ettaro sar&agrave; compresa tra un minimo di 60 ed un massimo di 150 piante.&nbsp;<br />&Egrave; vietata ogni somministrazione di fertilizzanti di sintesi ed il ricorso a fitofarmaci nella fase produttiva.&nbsp;<br />La raccolta potr&agrave; essere effettuata a mano o con mezzi meccanici idonei tali da salvaguardare l''integrit&agrave; del prodotto.&nbsp;<br />La produzione con l''IGP &laquo;Castagna del Monte Amiata&raquo;, non potr&agrave; superare la produzione massima di kg 12 (dodici) per pianta e di kg 1 800 (milleottocento) per ettaro.&nbsp;<br />Le operazioni di cernita, di calibratura, di trattamento e conservazione dei frutti, debbono essere effettuate nell''ambito del territorio di produzione cosi come delimitato al punto 4.3.&nbsp;<br />La conservazione del prodotto dovr&agrave; essere fatta mediante cura in acqua fredda per non
pi&ugrave; di sette giorni senza aggiunta di alcun additivo, o mediante sterilizzazione con bagno in acqua calda e successivo bagno in acqua fredda senza aggiunta di nessun additivo e secondo la corretta tecnica locale. &Egrave; ammessa la conservazione tramite surgelazione secondo le modalit&agrave; previste per i prodotti surgelati."</p><p style="text-align: justify;"><strong>Tipo di coltivazione:&nbsp;</strong>castagneto specializzato</p><p style="text-align: justify;"><strong>Metodo di controllo delle erbe infestanti:&nbsp;</strong>Taglio raso delle erbe tramite mezzi meccanici.<br />Non vengono usati diserbanti per eliminare l''erba del castagneto ma semplicemente dei "frullini" per rasarla nel periodo di preparazionbe alla raccolta.</p><p style="text-align: justify;"><strong>Metodo di controllo insetti e parassiti:&nbsp;</strong>Attualmente queste colture sono attaccate da un insetto "Cinipide" che risulta importato dalla Cina e contro il quale ancora non &egrave; stata trovata alcuna forma di
eliminazione.</p><p style="text-align: justify;"><strong>Metodo di controllo malattie crittogamiche:&nbsp;</strong>La malattia del "Cancro" del castagno che &egrave; la pi&amp; ugrave; diffusa, viene tenuta sotto controllo con potature periodiche e mirate.</p><p style="text-align: justify;"><strong>Metodo di concimazione:&nbsp;</strong>naturale- le foglie marcite dell''anno precedente - erba e ricci che marciscono nel terreno</p><p style="text-align: justify;"><strong>Fonti di approvvigionamento idrico:&nbsp;</strong>acquedotto</p><p style="text-align: justify;"><strong>Macchinari presenti in azienda:&nbsp;</strong>Decespugliatori, soffiatori, vaglio &hellip;&hellip;&hellip;&hellip;</p><p style="text-align: justify;"><strong>Note sulla coltivazione:&nbsp;</strong>Francesco ha scelto di perseverare con la raccolta a mano tradizionale, perch&egrave; con la macchina di raccolta il prodotto viene troppo stressato e si produce un inquinamento da gasolio esagerato.</p><p>&nbsp;</p><h3>MODALIT&Agrave; DI
TRASFORMAZIONE</h3><p style="text-align: justify;"><strong>Processo di trasformazione biologico:&nbsp;</strong>Parziale</p><p style="text-align: justify;"><strong>Stato certificazione biologica:&nbsp;Autocertificazione</strong></p><p style="text-align: justify;"><strong><strong>Motivo della scelta e note:&nbsp;</strong>Le castagne provengono tutte da una zona incontaminata dalla chimica perci&ograve; non &egrave; possibile una contaminazione delle farine</strong></p><p style="text-align: justify;"><strong><strong>Elenco dei prodotti trasformati:&nbsp;</strong>farina di castagne</strong></p><p style="text-align: justify;"><strong><strong>Fasi del processo di trasformazione:&nbsp;</strong>Trebbiatura e essiccazione: presso l''Agriturismo Sorripe di Paolo Frosolini a Montelaterone.<br />macinazione: presso il mulino del Bagnolo (frazione di Santa Fiora) piccolo e locale. Nella zona del monte Amiata (1800 ettari)nessun castagneto fa trattamenti, sono tutti iscritti all'' IGP perci&ograve; non &egrave; possibile
alcuna contaminazione&nbsp;<br />confezionamento: presso l'' Agriturismo Sorripe o Marzio Pasqui, via Casella Alta, Castel del Piano</strong></p><p style="text-align: justify;"><strong><strong>Metodo di conservazione:&nbsp;</strong>Il prodotto viene confezionato e chiuso, saldato a caldo. Viene posta l''etichetta con la scadenza a 12 mesi.</strong></p><p><strong><br /><h3>NOTE FINALI</h3><p style="text-align: justify;"><strong>NOTE del Produttore:&nbsp;</strong>A breve uscir&agrave; su Facebook l''appello ad una partecipazione attiva di altre famiglie con proposta di acquisto in condivisione dei fabbricati e dei terreni. Comunicheremo link.<br /><br />Anche l''allevamento di maiali &egrave; un''attivit&agrave; in crescita e verr&agrave; pubblicato quando ci saranno tutti i requisiti utili.<br /><br />OSSERVAZIONI DEL GRUPPO PRODUTTORI: il produttore &egrave; stato validato pur non avendo certificazione biologica perch&egrave; si trova in una zona che &egrave; caratterizzata dall''assenza totale di
trattamenti chimici alle piante e che &egrave; soggetta a disciplinare IGP e sottoposta a costanti controlli.</p></strong></p>', 'IT41W0888572190000000200117', 'fratellofiore@gmail.com', '320 3476257', 'LOCALITA'' LE FRATTE, 1', '58047 MONTICELLO AMIATA -CINIGIANO (GR)', '0000-00-00 00:00:00', '2013-01-22 00:42:05'),
(44, 'Pasta IRIS', '<p>&nbsp;</p><p>pasta</p><p>&nbsp;</p><p>&nbsp;</p>', '-', '-', '-', '-', '', '2012-11-28 23:27:17', '2012-11-28 23:27:17'),
(45, 'Editrice AAM Terra Nuova S.r.l.', '<p>Edizioni di libri legati alla cucina naturale, alla medicina naturale, alla sostenibilit&agrave;. Sconto 20% per i GAS.</p>', '-', 'servizioclienti@aamterranuova.it', 'Tel. +39 055 3215729', 'Via Ponte di Mezzo, 1', '50127 - Firenze', '2012-12-04 12:08:50', '2012-12-04 12:08:50'),
(46, 'Work Crossing cspa', '<p>Pasticceria del carcere di Padova, produce "I dolci di Giotto"</p>', 'IT36O0622512121074036', 'l.teruzzi@idolcidigiotto.it', '049 8033744 / 329 6372349', 'VIA FORCELLINI, 172 - 35128 PADOVA', '', '2012-12-20 14:23:05', '2012-12-20 14:23:05'),
(47, 'SOS Rosarno', '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', '-', '-', '-', '-', '', '2013-01-20 18:43:26', '2013-01-20 18:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `movimenti`
--

CREATE TABLE IF NOT EXISTS `movimenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_gasista` int(11) NOT NULL,
  `id_pagamento` int(11) NOT NULL,
  `importo` decimal(11,2) NOT NULL,
  `ds_nota` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_autore` int(11) NOT NULL,
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pagamenti`
--

CREATE TABLE IF NOT EXISTS `pagamenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_fornitore` int(11) NOT NULL,
  `importo` decimal(11,2) NOT NULL,
  `dt_pagamento` date NOT NULL,
  `id_causale` int(11) NOT NULL,
  `ds_nota` text COLLATE utf8_unicode_ci NOT NULL,
  `id_autore` int(11) NOT NULL,
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessid` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT '0',
  `firstts` int(11) NOT NULL DEFAULT '0',
  `lastts` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nm_nome` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `nm_cognome` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `ds_email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `ds_telefono` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fl_attivo` tinyint(4) NOT NULL DEFAULT '1',
  `fl_admin` tinyint(4) NOT NULL DEFAULT '0',
  `fl_contabile` tinyint(4) NOT NULL DEFAULT '0',
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nm_nome`, `nm_cognome`, `username`, `password`, `ds_email`, `ds_telefono`, `indirizzo_1`, `indirizzo_2`, `fl_attivo`, `fl_admin`, `fl_contabile`, `dt_ins`, `dt_agg`) VALUES
(1, 'admin', 'superuser', 'admin', 'admin', '-', '-', '-', '', 1, 1, 1, '2010-12-10 12:58:36', '2012-07-23 10:32:09'),
(0, 'Gasista GAS', 'Gasista GAS', 'gasdelsole', 'gasdelsole', '-', '-', '-', '', 1, 0, 1, '2011-08-29 22:35:47', '2012-06-06 23:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `versamenti`
--

CREATE TABLE IF NOT EXISTS `versamenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_gasista` int(11) NOT NULL,
  `importo` decimal(11,2) NOT NULL,
  `id_causale` int(11) NOT NULL,
  `ds_nota` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_autore` int(11) NOT NULL,
  `dt_versamento` date NOT NULL,
  `dt_ins` datetime NOT NULL,
  `dt_agg` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE  `users` ADD UNIQUE (
`username`
)

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
