SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS sitChamp (
  idChamp mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  idChampParent mediumint(3) unsigned DEFAULT NULL,
  libelle varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  stockage enum('xml','db','plugin') COLLATE utf8_unicode_ci NOT NULL,
  xpath text COLLATE utf8_unicode_ci,
  scope enum('fiche','groupe') COLLATE utf8_unicode_ci DEFAULT NULL,
  versioning enum('Y','N') COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  bordereau set('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci DEFAULT NULL,
  liste varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  identifiant varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  cle enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (idChamp),
  KEY idChampParent (idChampParent),
  KEY idxChampListe (liste) USING HASH,
  KEY idxChampIdentifiant (identifiant) USING BTREE,
  KEY idxChampXPath (xpath(192))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitCommune (
  codeInsee varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  codePostal varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  codePays varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FR',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  gpsLat float(10,6) DEFAULT NULL,
  gpsLng float(10,6) DEFAULT NULL,
  PRIMARY KEY (codeInsee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitCriteresBool (
  idFiche int(4) unsigned NOT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitCriteresDates (
  idFiche int(4) unsigned NOT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  date1 date NOT NULL,
  date2 date DEFAULT NULL,
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitCriteresInt (
  idFiche int(4) unsigned NOT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  valeur int(4) NOT NULL,
  min float(10,5) DEFAULT NULL,
  max float(10,5) DEFAULT NULL,
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitCriteresString (
  idFiche int(4) unsigned NOT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  valeur varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitEntreesThesaurus (
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  liste varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  lang enum('fr','en','de','es','nl','it') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arborescence varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  codeTIFv2_2 varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  codeThesaurus varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  actif enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (cle,liste,lang),
  KEY lang (lang),
  KEY cle (cle),
  KEY codeThesaurus (codeThesaurus),
  KEY libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitEntreesThesaurusMasque (
  idThesaurus mediumint(3) unsigned NOT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `Index 1` (idThesaurus,cle),
  KEY fk_cleThesaurus (cle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `sitEntreesThesaurusMasqueSum` (
`idThesaurus` mediumint(3) unsigned
,`masque` text
);
CREATE TABLE IF NOT EXISTS sitEntreesThesaurusStems (
  cle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  lang enum('fr','en','de','es','nl','it') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr',
  stem varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  UNIQUE KEY uniqueStem (stem,cle,lang),
  KEY fk_stem_cle (cle),
  KEY fk_stem_lang (lang),
  KEY key_stem (stem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFiche (
  idFiche int(4) unsigned NOT NULL AUTO_INCREMENT,
  codeTIF varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  codeInsee varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  bordereau enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  raisonSociale varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  gpsLat double(14,12) DEFAULT NULL,
  gpsLng double(14,12) DEFAULT NULL,
  idGroupe mediumint(3) unsigned DEFAULT NULL,
  ficheDeReference enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  referenceExterne varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  publication enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  dateCreation datetime NOT NULL,
  PRIMARY KEY (idFiche) USING BTREE,
  KEY codeInsee (codeInsee),
  KEY idGroupe (idGroupe)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitFicheChamps (
  idFiche int(4) unsigned NOT NULL,
  cle char(24) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY fk_fichechamp_unique (idFiche,cle) USING BTREE,
  KEY idx_fichechamp_cle (cle) USING HASH,
  KEY idx_fichechamp_fiche (idFiche) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFicheFichier (
  idFichier int(6) unsigned NOT NULL AUTO_INCREMENT,
  idFiche int(4) unsigned NOT NULL,
  md5 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  nomFichier varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  path varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  url varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('audio','doc','image','pdf','video','xml') COLLATE utf8_unicode_ci DEFAULT NULL,
  extension varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  proprietes varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  principal enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (idFichier),
  KEY idFiche (idFiche)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFichePublication (
  idFiche int(4) unsigned NOT NULL,
  idGroupe mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idFiche,idGroupe),
  KEY idGroupe (idGroupe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFicheSupprime (
  idFiche int(4) unsigned NOT NULL,
  codeTIF varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  codeInsee varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  bordereau enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  raisonSociale varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  idUtilisateur int(6) unsigned NOT NULL,
  ficheDeReference enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  referenceExterne varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  idFicheVersion mediumint(3) unsigned NOT NULL,
  dateCreation datetime NOT NULL,
  dateSuppression datetime NOT NULL,
  PRIMARY KEY (idFiche),
  KEY idUtilisateur (idUtilisateur),
  KEY codeInsee (codeInsee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFicheValeurChamp (
  idFiche int(4) unsigned NOT NULL,
  idFicheVersion int(6) unsigned NOT NULL DEFAULT '0',
  idChamp mediumint(3) unsigned NOT NULL,
  idGroupe mediumint(3) unsigned NOT NULL DEFAULT '0',
  valeur text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (idFiche,idFicheVersion,idChamp,idGroupe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFicheValidationChamp (
  idValidationChamp int(4) unsigned NOT NULL AUTO_INCREMENT,
  idFiche int(4) unsigned NOT NULL,
  idChamp mediumint(3) unsigned NOT NULL,
  valeur mediumtext COLLATE utf8_unicode_ci,
  idUtilisateur int(6) unsigned DEFAULT NULL,
  dateModification datetime NOT NULL,
  etat enum('a_valider','accepte','refuse') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a_valider',
  IdValidateur int(6) unsigned DEFAULT NULL,
  dateValidation datetime DEFAULT NULL,
  PRIMARY KEY (idValidationChamp),
  KEY idUtilisateur (idUtilisateur),
  KEY idFiche (idFiche),
  KEY idChamp (idChamp),
  KEY IdValidateur (IdValidateur)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitFicheVersion (
  idFicheVersion int(6) unsigned NOT NULL,
  idFiche int(4) unsigned NOT NULL,
  dateVersion datetime NOT NULL,
  idUtilisateur int(6) unsigned DEFAULT NULL,
  etat enum('brouillon','a_valider','accepte','refuse') COLLATE utf8_unicode_ci NOT NULL,
  dateValidation datetime DEFAULT NULL,
  PRIMARY KEY (idFicheVersion,idFiche) USING BTREE,
  KEY idUtilisateur (idUtilisateur) USING HASH,
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupe (
  idGroupe mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  idGroupeParent mediumint(3) unsigned DEFAULT NULL,
  nomGroupe varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  descriptionGroupe text COLLATE utf8_unicode_ci,
  idSuperAdmin int(6) unsigned DEFAULT NULL,
  PRIMARY KEY (idGroupe),
  KEY idSuperAdmin (idSuperAdmin),
  KEY idGroupeParent (idGroupeParent)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupePartenaire (
  idGroupe mediumint(3) unsigned NOT NULL,
  idGroupePartenaire mediumint(3) unsigned NOT NULL,
  typePartenaire enum('exclude','include') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'exclude',
  PRIMARY KEY (idGroupe,idGroupePartenaire),
  KEY idGroupePartenaire (idGroupePartenaire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupePartenaireFicheExclude (
  idGroupe mediumint(3) unsigned NOT NULL,
  idFiche int(4) unsigned NOT NULL,
  PRIMARY KEY (idGroupe,idFiche),
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupePartenaireFicheInclude (
  idGroupe mediumint(3) unsigned NOT NULL,
  idFiche int(4) unsigned NOT NULL,
  PRIMARY KEY (idGroupe,idFiche),
  KEY idFiche (idFiche)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupePlugin (
  idGroupe mediumint(3) unsigned NOT NULL,
  idPlugin smallint(2) unsigned NOT NULL,
  PRIMARY KEY (idGroupe,idPlugin),
  KEY idPlugin (idPlugin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitGroupeTerritoire (
  idGroupe mediumint(3) unsigned NOT NULL,
  idTerritoire mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idGroupe,idTerritoire),
  KEY idTerritoire (idTerritoire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitMapThesaurus (
  cleInterne varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cleExterne varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  thesaurus varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (cleInterne,cleExterne,thesaurus),
  KEY thesaurus (thesaurus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitPlugin (
  idPlugin smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  nomPlugin varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  version varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  actif enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  dateMaj datetime NOT NULL,
  PRIMARY KEY (idPlugin)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitProfilDroit (
  idProfil mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  idGroupe mediumint(3) unsigned DEFAULT NULL,
  libelle varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  droit int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (idProfil),
  KEY idGroupe (idGroupe)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitProfilDroitChamp (
  idProfil mediumint(3) unsigned NOT NULL,
  idChamp mediumint(3) unsigned NOT NULL,
  droit mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idProfil,idChamp),
  KEY idChamp (idChamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitSessions (
  idUtilisateur int(6) unsigned NOT NULL,
  sessionId varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  sessionStart datetime NOT NULL,
  sessionEnd datetime NOT NULL,
  ip varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY sessionId (sessionId) USING BTREE,
  KEY idUtilisateur (idUtilisateur) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitSessionsArchive (
  idUtilisateur int(6) unsigned NOT NULL,
  sessionId varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  sessionStart datetime NOT NULL,
  sessionEnd datetime NOT NULL,
  ip varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY sessionId (sessionId),
  KEY idUtilisateur (idUtilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitTerritoire (
  idTerritoire mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (idTerritoire)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitTerritoireCommune (
  codeInsee varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  idTerritoire mediumint(3) unsigned NOT NULL,
  prive enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (codeInsee,idTerritoire),
  KEY idTerritoire (idTerritoire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitTerritoireThesaurus (
  idTerritoire mediumint(3) unsigned NOT NULL,
  idThesaurus mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idTerritoire,idThesaurus),
  KEY idThesaurus (idThesaurus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS sitThesaurus (
  idThesaurus mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  prefixe smallint(2) unsigned DEFAULT NULL,
  codeThesaurus varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  libelle varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (idThesaurus),
  UNIQUE KEY prefixe (prefixe),
  KEY codeThesaurus (codeThesaurus)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitUtilisateur (
  idUtilisateur int(6) unsigned NOT NULL AUTO_INCREMENT,
  login varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  pass varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  idGroupe mediumint(3) unsigned DEFAULT NULL,
  typeUtilisateur enum('desk','admin','manager') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'admin',
  PRIMARY KEY (idUtilisateur),
  KEY idGroupeCreateur (idGroupe),
  KEY idxUserLogin (login),
  KEY idxUserPasswd (pass)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitUtilisateurDroitFiche (
  idUtilisateur int(6) unsigned NOT NULL,
  idFiche int(4) unsigned NOT NULL,
  idProfil mediumint(3) unsigned DEFAULT NULL,
  droit mediumint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (idUtilisateur,idFiche),
  KEY idFiche (idFiche),
  KEY idProfil (idProfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitUtilisateurDroitFicheChamp (
  idUtilisateur int(6) unsigned NOT NULL,
  idFiche int(4) unsigned NOT NULL,
  idChamp mediumint(3) unsigned NOT NULL DEFAULT '0',
  droit mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idUtilisateur,idFiche,idChamp),
  KEY idFiche (idFiche),
  KEY idChamp (idChamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitUtilisateurDroitTerritoire (
  idUtilisateur int(6) unsigned NOT NULL,
  bordereau enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  idTerritoire mediumint(3) unsigned NOT NULL,
  idProfil mediumint(3) unsigned DEFAULT NULL,
  droit mediumint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (idUtilisateur,bordereau,idTerritoire),
  KEY idTerritoire (idTerritoire),
  KEY idProfil (idProfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS sitUtilisateurDroitTerritoireChamp (
  idUtilisateur int(6) unsigned NOT NULL,
  bordereau enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  idTerritoire mediumint(3) unsigned NOT NULL,
  idChamp mediumint(3) unsigned NOT NULL DEFAULT '0',
  droit mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (idUtilisateur,bordereau,idTerritoire,idChamp),
  KEY idTerritoire (idTerritoire),
  KEY idChamp (idChamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;
DROP TABLE IF EXISTS `sitEntreesThesaurusMasqueSum`;

CREATE ALGORITHM=UNDEFINED DEFINER=root@`%` SQL SECURITY DEFINER VIEW tourismSystem2.sitEntreesThesaurusMasqueSum AS select tourismSystem2.sitEntreesThesaurusMasque.idThesaurus AS idThesaurus,group_concat(tourismSystem2.sitEntreesThesaurusMasque.cle separator '|') AS masque from tourismSystem2.sitEntreesThesaurusMasque group by tourismSystem2.sitEntreesThesaurusMasque.idThesaurus;


ALTER TABLE `sitChamp`
  ADD CONSTRAINT sitChamp_ibfk_1 FOREIGN KEY (idChampParent) REFERENCES sitChamp (idChamp) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitCriteresBool`
  ADD CONSTRAINT sitCriteresBool_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sitCriteresDates`
  ADD CONSTRAINT sitCriteresDates_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sitCriteresInt`
  ADD CONSTRAINT sitCriteresInt_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sitCriteresString`
  ADD CONSTRAINT sitCriteresString_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sitEntreesThesaurus`
  ADD CONSTRAINT sitEntreesThesaurus_ibfk_1 FOREIGN KEY (codeThesaurus) REFERENCES sitThesaurus (codeThesaurus) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitEntreesThesaurusMasque`
  ADD CONSTRAINT fk_thesoMasque_idThesaurus FOREIGN KEY (idThesaurus) REFERENCES sitThesaurus (idThesaurus);

ALTER TABLE `sitFiche`
  ADD CONSTRAINT sitFiche_ibfk_2 FOREIGN KEY (codeInsee) REFERENCES sitCommune (codeInsee) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT sitFiche_ibfk_3 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE `sitFicheChamps`
  ADD CONSTRAINT fk_fichechamp_idFiche FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE;

ALTER TABLE `sitFicheFichier`
  ADD CONSTRAINT sitFicheFichier_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `sitFichePublication`
  ADD CONSTRAINT sitFichePublication_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE,
  ADD CONSTRAINT sitFichePublication_ibfk_2 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE;

ALTER TABLE `sitFicheValidationChamp`
  ADD CONSTRAINT sitFicheValidationChamp_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitFicheValidationChamp_ibfk_10 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT sitFicheValidationChamp_ibfk_5 FOREIGN KEY (idChamp) REFERENCES sitChamp (idChamp) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitFicheVersion`
  ADD CONSTRAINT sitFicheVersion_ibfk_1 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitFicheVersion_ibfk_2 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `sitGroupe`
  ADD CONSTRAINT sitGroupe_ibfk_2 FOREIGN KEY (idGroupeParent) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE;

ALTER TABLE `sitGroupePartenaire`
  ADD CONSTRAINT sitGroupePartenaire_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE,
  ADD CONSTRAINT sitGroupePartenaire_ibfk_2 FOREIGN KEY (idGroupePartenaire) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE;

ALTER TABLE `sitGroupePartenaireFicheExclude`
  ADD CONSTRAINT sitGroupePartenaireFicheExclude_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE,
  ADD CONSTRAINT sitGroupePartenaireFicheExclude_ibfk_2 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE;

ALTER TABLE `sitGroupePartenaireFicheInclude`
  ADD CONSTRAINT sitGroupePartenaireFicheInclude_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE,
  ADD CONSTRAINT sitGroupePartenaireFicheInclude_ibfk_2 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE;

ALTER TABLE `sitGroupePlugin`
  ADD CONSTRAINT sitGroupePlugin_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitGroupePlugin_ibfk_2 FOREIGN KEY (idPlugin) REFERENCES sitPlugin (idPlugin) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitGroupeTerritoire`
  ADD CONSTRAINT sitGroupeTerritoire_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE,
  ADD CONSTRAINT sitGroupeTerritoire_ibfk_2 FOREIGN KEY (idTerritoire) REFERENCES sitTerritoire (idTerritoire) ON DELETE CASCADE;

ALTER TABLE `sitMapThesaurus`
  ADD CONSTRAINT sitMapThesaurus_ibfk_2 FOREIGN KEY (cleInterne) REFERENCES sitEntreesThesaurus (cle) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitMapThesaurus_ibfk_3 FOREIGN KEY (thesaurus) REFERENCES sitThesaurus (codeThesaurus) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitProfilDroit`
  ADD CONSTRAINT sitProfilDroit_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitProfilDroitChamp`
  ADD CONSTRAINT sitProfilDroitChamp_ibfk_1 FOREIGN KEY (idProfil) REFERENCES sitProfilDroit (idProfil) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitProfilDroitChamp_ibfk_2 FOREIGN KEY (idChamp) REFERENCES sitChamp (idChamp) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitSessions`
  ADD CONSTRAINT sitSessions_ibfk_1 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitTerritoireCommune`
  ADD CONSTRAINT sitTerritoireCommune_ibfk_1 FOREIGN KEY (codeInsee) REFERENCES sitCommune (codeInsee) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitTerritoireCommune_ibfk_2 FOREIGN KEY (idTerritoire) REFERENCES sitTerritoire (idTerritoire) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitTerritoireThesaurus`
  ADD CONSTRAINT sitTerritoireThesaurus_ibfk_1 FOREIGN KEY (idTerritoire) REFERENCES sitTerritoire (idTerritoire) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitTerritoireThesaurus_ibfk_2 FOREIGN KEY (idThesaurus) REFERENCES sitThesaurus (idThesaurus) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitUtilisateur`
  ADD CONSTRAINT sitUtilisateur_ibfk_1 FOREIGN KEY (idGroupe) REFERENCES sitGroupe (idGroupe) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `sitUtilisateurDroitFiche`
  ADD CONSTRAINT sitUtilisateurDroitFiche_ibfk_1 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitFiche_ibfk_2 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitFiche_ibfk_3 FOREIGN KEY (idProfil) REFERENCES sitProfilDroit (idProfil) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `sitUtilisateurDroitFicheChamp`
  ADD CONSTRAINT sitUtilisateurDroitFicheChamp_ibfk_1 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitFicheChamp_ibfk_2 FOREIGN KEY (idFiche) REFERENCES sitFiche (idFiche) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitFicheChamp_ibfk_5 FOREIGN KEY (idChamp) REFERENCES sitChamp (idChamp) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sitUtilisateurDroitTerritoire`
  ADD CONSTRAINT sitUtilisateurDroitTerritoire_ibfk_1 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitTerritoire_ibfk_2 FOREIGN KEY (idTerritoire) REFERENCES sitTerritoire (idTerritoire) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitTerritoire_ibfk_3 FOREIGN KEY (idProfil) REFERENCES sitProfilDroit (idProfil) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `sitUtilisateurDroitTerritoireChamp`
  ADD CONSTRAINT sitUtilisateurDroitTerritoireChamp_ibfk_1 FOREIGN KEY (idUtilisateur) REFERENCES sitUtilisateur (idUtilisateur) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitTerritoireChamp_ibfk_2 FOREIGN KEY (idTerritoire) REFERENCES sitTerritoire (idTerritoire) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT sitUtilisateurDroitTerritoireChamp_ibfk_5 FOREIGN KEY (idChamp) REFERENCES sitChamp (idChamp) ON DELETE CASCADE ON UPDATE CASCADE;
