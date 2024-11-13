<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" omit-xml-declaration="yes" indent="yes" encoding="UTF-8" version="1.0" />
	
	<xsl:param name="baseUrl"/>
	<xsl:param name="codeLangue"/>
	<xsl:param name="htmlTag"/>
	<xsl:param name="complexe"/>
	
	<xsl:variable name="traductions" select="document('translation.xml')/traductions" />

	<xsl:template match="/fiche">
		
		<xsl:if test="$htmlTag = 'true'">
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>
				<body>
					<xsl:call-template name="contenu" />
				</body>
			</html>
		</xsl:if>
		
		<xsl:if test="$htmlTag != 'true'">
			<xsl:call-template name="contenu" />
		</xsl:if>
		
	</xsl:template>	
	
	<xsl:template name="contenu">		
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;language=fr"></script>

		<xsl:if test="$complexe = 'true'">
			<script type="text/javascript" src="{$baseUrl}ressources/ficheHTML/gallery/gallery.js"><xsl:text> </xsl:text></script>
		</xsl:if>
		
		<div id="tourismeConteneur">
			
			<xsl:element name="a">
				<xsl:attribute name="id">topFiche</xsl:attribute>
				<xsl:attribute name="name">topFiche</xsl:attribute>
				<xsl:text> </xsl:text>
			</xsl:element>
			
			<div class="detailFiche">
				<!--div class="detailTitreFiche">
					< Raison sociale >
					<div class="titre">
						<xsl:for-each select="raison_sociale">
							<xsl:copy-of select="./text()"/>
						</xsl:for-each>
					</div>
					
					< Classements >
					<div class="classement"><xsl:text> </xsl:text>
						<xsl:for-each select="classement">
							<xsl:call-template name="traduireTIF">
								<xsl:with-param name="codeTIF">
									<xsl:copy-of select="./text()"/>< appeler la méthode php qui traduit les champs tif >
								</xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</div>
				</div-->
			
				<xsl:element name="table">
					<xsl:attribute name="class">
						<xsl:text>detailTab</xsl:text>
					</xsl:attribute>
					<tr>
						<xsl:element name="td">
							<xsl:attribute name="class">
								<xsl:text>detailTabGauche</xsl:text>
							</xsl:attribute>
							
							<xsl:element name="div">
								<xsl:attribute name="class">
									<xsl:text>detailHeader</xsl:text>
									<xsl:if test="count(/fiche/fonctionnalites[text()='avisInternautes'])=1">
										<xsl:if test="count(/fiche/avis/liste/avis_item)>0">
											<xsl:text>AvecAvis</xsl:text>
										</xsl:if>
									</xsl:if>
								</xsl:attribute>
								<!-- Image principale -->
								<div class="detailPhoto"><xsl:text> </xsl:text>
									<xsl:if test="count(photos_fichiers[1]/url_fichier)>=1">
										<xsl:choose>
											<xsl:when test="count(/fiche/fonctionnalites[text()='avisInternautes'])=0 or count(/fiche/avis/liste/avis_item)=0">
												<xsl:element name="a">
													<xsl:attribute name="href">
														<xsl:text>#photos</xsl:text>
													</xsl:attribute>
													<xsl:element name="img">
														<xsl:attribute name="src">
															<xsl:call-template name="getImageListe">
																<xsl:with-param name="srcImage">
																	<xsl:copy-of select="photos_fichiers[1]/url_fichier/text()"/>
																</xsl:with-param>
															</xsl:call-template>
														</xsl:attribute>
														<xsl:attribute name="alt"><xsl:copy-of select="photos_fichiers[1]/nom_fichier/text()"/></xsl:attribute>
													</xsl:element>
												</xsl:element>
											</xsl:when>
											<xsl:otherwise>
												<xsl:element name="a">
													<xsl:attribute name="href">
														<xsl:text>#photos</xsl:text>
													</xsl:attribute>
													<xsl:element name="img">
														<xsl:attribute name="src">
															<xsl:call-template name="getImagePrinkAvis">
																<xsl:with-param name="srcImage">
																	<xsl:copy-of select="photos_fichiers[1]/url_fichier/text()"/>
																</xsl:with-param>
															</xsl:call-template>
														</xsl:attribute>
														<xsl:attribute name="alt"><xsl:copy-of select="photos_fichiers[1]/nom_fichier/text()"/></xsl:attribute>
													</xsl:element>
												</xsl:element>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:if>
								</div>
								<div class="detailHeaderFiche">
									<div class="titre">
										<xsl:for-each select="raison_sociale">
											<xsl:copy-of select="./text()"/>
										</xsl:for-each>
									</div>
									<div class="detailAdresse"><xsl:text> </xsl:text>
										<xsl:call-template name="deposeAdresseEtTel"/>
									</div>
									<xsl:if test="count(/fiche/site_web)>0">
										<xsl:if test="string-length(/fiche/site_web/text())>0">
											<div class="LienSiteWeb">
												<xsl:element name="a">
													<xsl:attribute name="href">
														<xsl:copy-of select="/fiche/site_web"/>
													</xsl:attribute>
													<xsl:call-template name="traduire">
														<xsl:with-param name="texte">Site Internet</xsl:with-param>
													</xsl:call-template>
												</xsl:element>
											</div>
										</xsl:if>
									</xsl:if>
									<xsl:if test="/fiche/capacite_personne/text()!=''">
										<div class="detailCapaciteGlobale">
											<span class="detailCapaciteLabel">
												<xsl:call-template name="traduire">
													<xsl:with-param name="texte">
														<xsl:text>Capacité : </xsl:text>
													</xsl:with-param>
												</xsl:call-template>
											</span>
											<xsl:copy-of select="/fiche/capacite_personne/text()"/>
										</div>
									</xsl:if>
									<xsl:if test="count(/fiche/chaine/cle)>0">
										<div class="detailLogoPrincipal"><xsl:text> </xsl:text>
											<span class="left"><xsl:text> </xsl:text></span>
											<span class="middle">
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="/fiche/chaine[1]/cle/text()"/><!-- php>tif -->
													</xsl:with-param>
												</xsl:call-template>
											</span>
											<span class="right"><xsl:text> </xsl:text></span>
										</div>
									</xsl:if>
									<xsl:element name="div">
										<xsl:attribute name="class">
											<xsl:text>detailIcones</xsl:text>
											<xsl:if test="count(photos_fichiers[1]/url_fichier)=0">
												<xsl:if test="count(/fiche/fonctionnalites[text()='avisInternautes'])=0 or count(/fiche/avis/liste/avis_item)=0">
													<xsl:text> pasPhotoPasAvis</xsl:text>
												</xsl:if>
											</xsl:if>
										</xsl:attribute>
										<xsl:if test="count(/fiche/label/cle)>0">
											<div class="detailLabel">
												<ul>
													<xsl:for-each select="label">
														<li>
															<xsl:call-template name="traduireTIF">
																<xsl:with-param name="codeTIF">
																	<xsl:copy-of select="./cle/text()"/><!-- php>tif -->
																</xsl:with-param>
															</xsl:call-template>
														</li>
													</xsl:for-each>
												</ul>
											</div>
										</xsl:if>
										<div class="detailLogoEtLangues">
											<xsl:if test="count(/fiche/langues_parlees_accueil/langue)>0">
												<xsl:if test="count(/fiche/fonctionnalites[text()='avisInternautes'])=0 or count(/fiche/avis/liste/avis_item)=0">
													<div class="langues">
														<ul>
															<xsl:for-each select="/fiche/langues_parlees_accueil">
																<li>
																	<xsl:call-template name="traduire">
																		<xsl:with-param name="texte">
																			<xsl:copy-of select="./langue/text()"/><!-- traduire -->
																		</xsl:with-param>
																	</xsl:call-template>
																</li>
															</xsl:for-each>
														</ul>
													</div>
												</xsl:if>
											</xsl:if>
											<xsl:if test="count(/fiche/handicap/cle)>0">
												<div class="detailLogo">
													<ul>
														<xsl:for-each select="handicap">
															<li>
																<xsl:call-template name="traduireTIF">
																	<xsl:with-param name="codeTIF">
																		<xsl:copy-of select="./cle/text()"/><!-- php>tif -->
																	</xsl:with-param>
																</xsl:call-template>
															</li>
														</xsl:for-each>
													</ul>
												</div>
											</xsl:if>
										</div>
									</xsl:element>
								</div>
							</xsl:element>
							<!-- contenu de la div detailHeader -->
							
						</xsl:element>
						<xsl:if test="count(/fiche/fonctionnalites[text()='avisInternautes'])=1">
							<xsl:if test="count(/fiche/avis/liste/avis_item)>0">
								<xsl:element name="td">
									<xsl:attribute name="class">
										<xsl:text>detailTabCentre</xsl:text>
									</xsl:attribute>
									<xsl:text> </xsl:text>
								</xsl:element>
								<xsl:element name="td">
									<xsl:attribute name="class">
										<xsl:text>detailTabDroite</xsl:text>
									</xsl:attribute>
									
									<div class="blocAvisHeader">
										<div class="nombreAvis">
											<div class="iconeAvis">
												<xsl:element name="img">
													<xsl:attribute name="src">
														<xsl:text>/sites/all/modules/_raccourci/tourism_raccourci/isc/images/avis/45/smiley_</xsl:text>
														<xsl:copy-of select="/fiche/avis/general/moyenne/text()"/>
														<xsl:text>.png</xsl:text>
													</xsl:attribute>
												</xsl:element>
											</div>
											<div class="nbAvis">
												<xsl:copy-of select="count(/fiche/avis/liste/avis_item)"/>
												<xsl:text> avis</xsl:text>
											</div>
										</div>
										<div class="unAvis">
											<div class="smileyAvis">
												<xsl:element name="img">
													<xsl:attribute name="src">
														<xsl:text>/sites/all/modules/_raccourci/tourism_raccourci/isc/images/avis/26/smiley_liste_</xsl:text>
														<xsl:copy-of select="substring(/fiche/avis/liste/avis_item[1]/note/text(),1,1)"/>
														<xsl:text>.png</xsl:text>
													</xsl:attribute>
												</xsl:element>
											</div>
											<div class="auteurAvis">
												<xsl:copy-of select="/fiche/avis/liste/avis_item[1]/auteur/text()"/>
											</div>
											<div class="dateAvis">
												<xsl:call-template name="getDateRemaniee">
													<xsl:with-param name="laDate">
														<xsl:copy-of select="/fiche/avis/liste/avis_item[1]/date/text()"/>
													</xsl:with-param>
												</xsl:call-template>
											</div>
											<div class="texteAvis">
												<xsl:text>"</xsl:text>
												<xsl:call-template name="coupure-texte">
													<xsl:with-param name="texte" select="/fiche/avis/liste/avis_item[1]/texte/text()"/>
													<xsl:with-param name="longueur" select="100"/>						
												</xsl:call-template>
												<xsl:text>"</xsl:text>
											</div>
										</div>
										<div class="tousAvis">
											<a href="#avis">
												<xsl:call-template name="traduire">
													<xsl:with-param name="texte">
														<xsl:text>Voir tous les avis</xsl:text>
													</xsl:with-param>
												</xsl:call-template>
											</a>
										</div>
									</div>
									<!-- contenu de la div blocAvisHeader -->
									
								</xsl:element>
							</xsl:if>
						</xsl:if>
					</tr>
				</xsl:element>
										
				<xsl:call-template name="detailOnglets">
					<xsl:with-param name="nomOnglet">descriptif</xsl:with-param>
				</xsl:call-template>
										
				<div class="detailFicheContent">
					<div class="detailDescription">
						<xsl:copy-of select="description_commerciale_fr/text()"/>
						<xsl:text> </xsl:text>
					</div>
					<div class="prestations">
						<xsl:if test="string-length(activite[1]/cle/text())>0">
							<fieldset>
								<legend>
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Activités</xsl:with-param>
									</xsl:call-template>
								</legend>
								<ul>
									<xsl:for-each select="activite">
										<li>
											<xsl:call-template name="traduireTIF">
												<xsl:with-param name="codeTIF">
													<xsl:copy-of select="./cle/text()"/> <!-- php>tif -->
												</xsl:with-param>
											</xsl:call-template>
											<xsl:if test="string-length(./distance/text())>0">
												<xsl:text> (</xsl:text>
												<xsl:copy-of select="./distance/text()"/>
												<xsl:text> </xsl:text>
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="./unite/text()"/> <!-- php>tif -->
													</xsl:with-param>
												</xsl:call-template>
												<xsl:text>)</xsl:text>
											</xsl:if>
										</li>
									</xsl:for-each>
								</ul>
							</fieldset>
						</xsl:if>
						<xsl:if test="string-length(confort[1]/cle/text())>0">
							<fieldset>
								<legend>
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Conforts</xsl:with-param>
									</xsl:call-template>
								</legend>
								<ul>
									<xsl:for-each select="confort">
										<li>
											<xsl:call-template name="traduireTIF">
												<xsl:with-param name="codeTIF">
													<xsl:copy-of select="./cle/text()"/> <!-- php>tif -->
												</xsl:with-param>
											</xsl:call-template>
											<xsl:if test="string-length(./distance/text())>0">
												<xsl:text> (</xsl:text>
												<xsl:copy-of select="./distance/text()"/>
												<xsl:text> </xsl:text>
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="./unite/text()"/> <!-- php>tif -->
													</xsl:with-param>
												</xsl:call-template>
												<xsl:text>)</xsl:text>
											</xsl:if>
										</li>
									</xsl:for-each>
								</ul>
							</fieldset>
						</xsl:if>
						<xsl:if test="string-length(equipement[1]/cle/text())>0">
							<fieldset>
								<legend>
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Equipements</xsl:with-param>
									</xsl:call-template>
								</legend>
								<ul>
									<xsl:for-each select="equipement">
										<li>
											<xsl:call-template name="traduireTIF">
												<xsl:with-param name="codeTIF">
													<xsl:copy-of select="./cle/text()"/> <!-- php>tif -->
												</xsl:with-param>
											</xsl:call-template>
											<xsl:if test="string-length(./distance/text())>0">
												<xsl:text> (</xsl:text>
												<xsl:copy-of select="./distance/text()"/>
												<xsl:text> </xsl:text>
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="./unite/text()"/> <!-- php>tif -->
													</xsl:with-param>
												</xsl:call-template>
												<xsl:text>)</xsl:text>
											</xsl:if>
										</li>
									</xsl:for-each>
								</ul>
						</fieldset>
						</xsl:if>
						<xsl:if test="string-length(service[1]/cle/text())>0">
							<fieldset>
								<legend>
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Services</xsl:with-param>
									</xsl:call-template>
								</legend>
								<ul>
									<xsl:for-each select="service">
										<li>
											<xsl:call-template name="traduireTIF">
												<xsl:with-param name="codeTIF">
													<xsl:copy-of select="./cle/text()"/> <!-- php>tif -->
												</xsl:with-param>
											</xsl:call-template>
											<xsl:if test="string-length(./distance/text())>0">
												<xsl:text> (</xsl:text>
												<xsl:copy-of select="./distance/text()"/>
												<xsl:text> </xsl:text>
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="./unite/text()"/> <!-- php>tif -->
													</xsl:with-param>
												</xsl:call-template>
												<xsl:text>)</xsl:text>
											</xsl:if>
										</li>
									</xsl:for-each>
								</ul>
							</fieldset>
						</xsl:if>
					</div>
					
					<!-- PHOTOS -->
					<xsl:if test="count(/fiche/photos_fichiers/url_fichier)>=1">
						<xsl:call-template name="detailOnglets">
							<xsl:with-param name="nomOnglet">photos</xsl:with-param>
						</xsl:call-template>
						
						
						<xsl:element name="div">
							<xsl:attribute name="id">gallery<xsl:value-of select="./idFiche/text()" /></xsl:attribute>
							<xsl:attribute name="class">gallery</xsl:attribute>
							<xsl:if test="$complexe = 'true'">
								<xsl:attribute name="style">width:664px;height:498px;</xsl:attribute>
							</xsl:if>
							<!--ul>
								<li>
									Photos
								</li>
								<xsl:for-each select="fonctionnalites">
									<xsl:if test="text()='videos'">
										<li>
											<xsl:text>Videos</xsl:text>
										</li>
									</xsl:if>
								</xsl:for-each>
								<xsl:for-each select="fonctionnalites">
									<xsl:if test="text()='visitesVirtuelles'">
										<li>
											<xsl:text>Visites virtuelles</xsl:text>
										</li>
									</xsl:if>
								</xsl:for-each>
							</ul-->
							<xsl:element name="div">
								<xsl:attribute name="id">gallery<xsl:value-of select="./idFiche/text()" />Image</xsl:attribute>
								<xsl:attribute name="class">galleryImage</xsl:attribute>
								<xsl:text> </xsl:text>
							</xsl:element>
							<xsl:element name="div">
								<xsl:attribute name="id">gallery<xsl:value-of select="./idFiche/text()" />Navigation</xsl:attribute>
								<xsl:attribute name="class">galleryNavigation</xsl:attribute>
								<xsl:element name="ul">
									<xsl:attribute name="id">gallery<xsl:value-of select="./idFiche/text()" />NavigationScroll</xsl:attribute>
									<xsl:attribute name="class">galleryNavigationScroll</xsl:attribute>
									<xsl:for-each select="/fiche/photos_fichiers[type_fichier/text()='03.01.01']">
										<li>
											<xsl:element name="img">
												<xsl:attribute name="src">
													<xsl:call-template name="getImageDetailLarge">
														<xsl:with-param name="srcImage">
															<xsl:copy-of select="./url_fichier/text()"/>
														</xsl:with-param>
													</xsl:call-template>
												</xsl:attribute>
												<xsl:attribute name="alt"><xsl:copy-of select="./nom_fichier/text()"/></xsl:attribute>
											</xsl:element>
										</li>
									</xsl:for-each>
								</xsl:element>
							</xsl:element>
						</xsl:element>
						<!-- </div> -->
						<xsl:if test="$complexe = 'true'">
							<xsl:element name="script">
								<xsl:attribute name="type">text/javascript</xsl:attribute>
								var gallery = new jsGallery('gallery<xsl:value-of select="./idFiche/text()" />', {
									thumbWidth: 77,
									thumbHeight: 60,
									imgArrowWidth: 43,
									imgArrowHeight: 42,
									imgWidth: 664,
									imgHeight: 498,
									cols: 8,
									rows: 1	
								});
							</xsl:element>
						</xsl:if>
					</xsl:if>
					
					<!-- LOCALISATION -->
					<xsl:if test="count(./gps_lat) != 0 and ./gps_lat/text() != '' and count(./gps_lng) != 0 and ./gps_lng/text() != ''">
					
						<xsl:call-template name="detailOnglets">
							<xsl:with-param name="nomOnglet">localisation</xsl:with-param>
						</xsl:call-template>
						
						<xsl:if test="$complexe = 'true'">
							<xsl:element name="div">
								<xsl:attribute name="id">detailCarteGoogleMap<xsl:value-of select="./idFiche/text()" /></xsl:attribute>
								<xsl:attribute name="class">detailCarteGoogleMap</xsl:attribute>
								<xsl:text> </xsl:text>
							</xsl:element>
							<xsl:element name="script">
								<xsl:attribute name="type">text/javascript</xsl:attribute>								
								var map = new google.maps.Map(document.getElementById('detailCarteGoogleMap<xsl:value-of select="./idFiche/text()" />'), {
									mapTypeId: google.maps.MapTypeId.ROADMAP,
									zoom: 12
								});

								var latLng = new google.maps.LatLng(<xsl:value-of select="./gps_lat/text()" />, <xsl:value-of select="./gps_lng/text()" />);
								
								var marker = new google.maps.Marker({
									position: latLng,
									map: map,
									draggable: false,
									clickable: true,
									icon: '<xsl:value-of select="$baseUrl"/>ressources/ficheHTML/map/marker_generic.png'
								});
								
								map.setCenter(latLng);
							</xsl:element>
						</xsl:if>
						
						<xsl:if test="$complexe != 'true'">
							<div id="detailCarteGoogleMap" class="detailCarteGoogleMap">
								<xsl:element name="img">
									<xsl:attribute name="src">
										<xsl:call-template name="getStaticMap">
											<xsl:with-param name="gpsLat">
												<xsl:value-of select="./gps_lat/text()" />
											</xsl:with-param>
											<xsl:with-param name="gpsLng">
												<xsl:value-of select="./gps_lng/text()" />
											</xsl:with-param>
										</xsl:call-template>
									</xsl:attribute>
									<xsl:attribute name="alt">localisation</xsl:attribute>
								</xsl:element>
							</div>
						</xsl:if>
						
					</xsl:if>
					
					<xsl:if test="count(/fiche/avis/liste/avis_item)>0">
						<xsl:call-template name="detailOnglets">
							<xsl:with-param name="nomOnglet">avis</xsl:with-param>
						</xsl:call-template>
						<div class="detailAvis">
							<div class="detailEnteteAvis">
								<div class="imageHotel">
									<xsl:element name="img">
										<xsl:attribute name="src">
											<xsl:call-template name="getImageAvis">
												<xsl:with-param name="srcImage">
													<xsl:copy-of select="photos_fichiers[1]/url_fichier/text()"/>
												</xsl:with-param>
											</xsl:call-template>
										</xsl:attribute>
									</xsl:element>
								</div>
								<div class="detailHotel">
									<a href="#top">
										<span class="nomHotel">
											<xsl:copy-of select="/fiche/raison_sociale/text()"/>
										</span>
									</a>
									<span class="lieuHotel">
										<xsl:copy-of select="/fiche/commune/text()"/>
									</span>
								</div>
								<div class="nombreAvis">
									<div class="iconeAvis">
										<xsl:element name="img">
											<xsl:attribute name="src">
												<xsl:text>/sites/all/modules/_raccourci/tourism_raccourci/isc/images/avis/45/smiley_</xsl:text>
												<xsl:copy-of select="/fiche/avis/general/moyenne/text()"/>
												<xsl:text>.png</xsl:text>
											</xsl:attribute>
										</xsl:element>
									</div>
									<div class="nbAvis">
										<xsl:copy-of select="count(/fiche/avis/liste/avis_item)"/>
										<xsl:text> avis</xsl:text>
									</div>
								</div>
							</div>
							<div class="detailListeAvis">
								<xsl:for-each select="/fiche/avis/liste/avis_item">
									<div class="avis">
										<div class="icone">
											<xsl:element name="img">
												<xsl:attribute name="src">
													<xsl:text>/sites/all/modules/_raccourci/tourism_raccourci/isc/images/avis/32/smiley_</xsl:text>
													<xsl:copy-of select="substring(./note/text(),1,1)"/>
													<xsl:text>.png</xsl:text>
												</xsl:attribute>
											</xsl:element>
										</div>
										<div class="descriptionAvis">
											<span class="titreAvis">
												<xsl:text>"</xsl:text>
												<xsl:copy-of select="./titre/text()"/>
												<xsl:text>"</xsl:text>
											</span>
											<span class="auteurAvis">
												<xsl:copy-of select="./auteur/text()"/>
											</span>
											<span class="dateAvis">
												<xsl:call-template name="getDateRemaniee">
													<xsl:with-param name="laDate">
														<xsl:copy-of select="./date/text()"/>
													</xsl:with-param>
												</xsl:call-template>
											</span>
										</div>
										<div class="texteAvis">
											<xsl:copy-of select="./texte/text()"/>
										</div>
									</div>
								</xsl:for-each>
							</div>
						</div>
					</xsl:if>
					
					<xsl:if test="(string-length(/fiche/ouverture[1]/type/text())>0) or (count(/fiche/disponibilites/disponibilite)>=1) or (count(/fiche/mode_paiement/cle)>0) or (count(/fiche/tarif/type_tarif)>0)">
						<xsl:call-template name="detailOnglets">
							<xsl:with-param name="nomOnglet">dispo</xsl:with-param>
						</xsl:call-template>
						
						
						<xsl:if test="string-length(ouverture[1]/type/text())>0">
							<div class="periode">
								<div class="periodelibelle">
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Période(s) d'ouverture :</xsl:with-param>
									</xsl:call-template>
								</div>
								<ul>
									<xsl:for-each select="ouverture">
										<li>
											<span class="label">
												<xsl:call-template name="traduireTIF">
													<xsl:with-param name="codeTIF">
														<xsl:copy-of select="./type/text()"/>
													</xsl:with-param>
												</xsl:call-template>
											</span>
											<xsl:text> : </xsl:text>
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">du</xsl:with-param>
											</xsl:call-template>
											<xsl:text> </xsl:text>
											<xsl:call-template name="getDateRemaniee">
												<xsl:with-param name="laDate">
													<xsl:copy-of select="./datedebut/text()"/>
												</xsl:with-param>
											</xsl:call-template>
											<xsl:text> </xsl:text>
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">au</xsl:with-param>
											</xsl:call-template>
											<xsl:text> </xsl:text>
											<xsl:call-template name="getDateRemaniee">
												<xsl:with-param name="laDate">
													<xsl:copy-of select="./datefin/text()"/>
												</xsl:with-param>
											</xsl:call-template>
										</li>
									</xsl:for-each>
								</ul>
							</div>
						</xsl:if>
						
						<xsl:if test="count(tarif/type_tarif)>0">
							<div class="tarifs">
								<table cellspacing="0">
									<tr>
										<th class="libelle">
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">Tarifs</xsl:with-param>
											</xsl:call-template>
										</th>
										<th class="min">
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">Min</xsl:with-param>
											</xsl:call-template>
										</th>
										<th class="max">
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">Max</xsl:with-param>
											</xsl:call-template>
										</th>
										<th class="description">
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">Description</xsl:with-param>
											</xsl:call-template>
										</th>
									</tr>
										<xsl:for-each select="tarif">
											<xsl:element name="tr">
												<xsl:attribute name="class">
												<xsl:choose>
													<xsl:when test="(position() mod 2)=0">
														<xsl:text>ligne1</xsl:text>
													</xsl:when>
													<xsl:otherwise>
														<xsl:text>ligne2</xsl:text>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:if test="position()=count(/fiche/tarif)">
													<xsl:text> last</xsl:text>
												</xsl:if>
												<xsl:if test="position()=1">
													<xsl:text> first</xsl:text>
												</xsl:if>
												</xsl:attribute>
												<td class="libelle">
													<xsl:call-template name="traduireTIF">
														<xsl:with-param name="codeTIF">
															<xsl:copy-of select="./type_tarif/text()"/>
														</xsl:with-param>
													</xsl:call-template>
												</td>
												<xsl:element name="td">
													<xsl:if test="string-length(./tarifstandard/text())>0">
														<xsl:attribute name="colspan">
															<xsl:text>2</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="class">
															<xsl:text>minmax</xsl:text>
														</xsl:attribute>
														<xsl:copy-of select="./tarifstandard/text()"/>
														<xsl:if test="./tarifstandard/text()!='Incluse'">
															<xsl:text> €</xsl:text>
														</xsl:if>
													</xsl:if>
													<xsl:if test="string-length(./tarifstandard/text())=0">
														<xsl:attribute name="class">
															<xsl:text>min</xsl:text>
														</xsl:attribute>
													</xsl:if>
													<xsl:if test="string-length(./tarifstandard/text())=0">
														<xsl:if test="string-length(./tarifmin/text())>0">
															<xsl:copy-of select="./tarifmin/text()"/>
															<xsl:text> €</xsl:text>
														</xsl:if>
														<xsl:if test="string-length(./tarifmin/text())=0">
															<xsl:text>/</xsl:text>
														</xsl:if>
													</xsl:if>
												</xsl:element>
												<xsl:if test="string-length(./tarifstandard/text())=0">
													<td class="max">
														<xsl:if test="string-length(./tarifmax/text())>0">
															<xsl:copy-of select="./tarifmax/text()"/>
															<xsl:text> €</xsl:text>
														</xsl:if>
														<xsl:if test="string-length(./tarifmax/text())=0">
															<xsl:text>/</xsl:text>
														</xsl:if>
													</td>
												</xsl:if>
												<td class="description">
													<xsl:copy-of select="./description_fr/text()"/>
												</td>
											</xsl:element>
										</xsl:for-each>				
								</table>
								<!-- attention ligne 1 & 2 -->
								<!-- tarif standard ? -->
							</div>
						</xsl:if>
						<xsl:if test="count(mode_paiement/cle)>0">
							<div class="paiement">
								<h3>
									<xsl:call-template name="traduire">
										<xsl:with-param name="texte">Mode(s) de paiement</xsl:with-param>
									</xsl:call-template>
								</h3>
								<ul>
									<xsl:for-each select="mode_paiement">
										<li>
											<xsl:call-template name="traduireTIF">
												<xsl:with-param name="codeTIF">
													<xsl:copy-of select="./cle/text()"/><!-- php>tif -->
												</xsl:with-param>
											</xsl:call-template>
										</li>
									</xsl:for-each>
								</ul>
							</div>
						</xsl:if>
					</xsl:if>
				</div>
			</div>
		</div>
		
		<link type="text/css" rel="stylesheet" media="all" href="{$baseUrl}ressources/ficheHTML/detail.css" />
	
	</xsl:template>


	<xsl:template name="detailOnglets">
		<xsl:param name="nomOnglet"/>
		
		<xsl:element name="a">
			<xsl:attribute name="name">
				<xsl:copy-of select="$nomOnglet"/>
			</xsl:attribute>
			<xsl:attribute name="id">
				<xsl:copy-of select="$nomOnglet"/>
			</xsl:attribute>
			<xsl:text> </xsl:text>
		</xsl:element>
		
		<div class="detailOnglet">
			<table>
				<tr>
					
					<td class="boutonretour">
						<xsl:element name="a">
							<xsl:attribute name="href">
								<xsl:text>#topFiche</xsl:text>
							</xsl:attribute>
							<xsl:text> </xsl:text>
						</xsl:element>
					</td>
					
					<xsl:element name="td">
						<xsl:if test="$nomOnglet='descriptif'">
							<xsl:attribute name="class">
								<xsl:text>ongletActif</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:element name="a">
							<xsl:attribute name="href">
								<xsl:text>#descriptif</xsl:text>
							</xsl:attribute>
							<xsl:call-template name="traduire">
								<xsl:with-param name="texte">Descriptif</xsl:with-param>
							</xsl:call-template>
						</xsl:element>
					</xsl:element>
					
					<xsl:if test="count(/fiche/photos_fichiers/url_fichier)>=1">
						<xsl:element name="td">
							<xsl:if test="$nomOnglet='photos'">
								<xsl:attribute name="class">
									<xsl:text>ongletActif</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:element name="a">
								<xsl:attribute name="href">
									<xsl:text>#photos</xsl:text>
								</xsl:attribute>
								<xsl:call-template name="traduire">
									<xsl:with-param name="texte">Photos</xsl:with-param>
								</xsl:call-template>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<xsl:if test="count(/fiche/gps_lat) != 0 and /fiche/gps_lat/text() != '' and count(/fiche/gps_lng) != 0 and /fiche/gps_lng/text() != ''">
						<xsl:element name="td">
							<xsl:if test="$nomOnglet='localisation'">
								<xsl:attribute name="class">
									<xsl:text>ongletActif</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:element name="a">
								<xsl:attribute name="href">
									<xsl:text>#localisation</xsl:text>
								</xsl:attribute>
								<xsl:call-template name="traduire">
									<xsl:with-param name="texte">Localisation</xsl:with-param>
								</xsl:call-template>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<xsl:if test="count(/fiche/avis/liste/avis_item)>0">
						<xsl:element name="td">
							<xsl:if test="$nomOnglet='avis'">
								<xsl:attribute name="class">
									<xsl:text>ongletActif</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:element name="a">
								<xsl:attribute name="href">
									<xsl:text>#avis</xsl:text>
								</xsl:attribute>
								<xsl:call-template name="traduire">
									<xsl:with-param name="texte">Avis</xsl:with-param>
								</xsl:call-template>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<xsl:if test="(string-length(/fiche/ouverture[1]/type/text())>0) or (count(/fiche/disponibilites/disponibilite)>=1) or (count(/fiche/mode_paiement/cle)>0) or (count(/fiche/tarif/type_tarif)>0)">
						<xsl:element name="td">
							<xsl:if test="$nomOnglet='dispo'">
								<xsl:attribute name="class">
									<xsl:text>ongletActif</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:element name="a">
								<xsl:attribute name="href">
									<xsl:text>#dispo</xsl:text>
								</xsl:attribute>
								<xsl:call-template name="traduire">
									<xsl:with-param name="texte">Disponibilités / Tarifs</xsl:with-param>
								</xsl:call-template>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<xsl:if test="/fiche/cs_urlresa/text()!=''">
						<xsl:element name="td">
							<xsl:element name="a">
								<xsl:attribute name="href">
									<xsl:copy-of select="/fiche/cs_urlresa/text()"/>
								</xsl:attribute>
								<xsl:attribute name="class">
									<xsl:text>reserver</xsl:text>
								</xsl:attribute>
								<xsl:call-template name="traduire">
									<xsl:with-param name="texte">Réserver</xsl:with-param>
								</xsl:call-template>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<td class="boutonretour2">
						<xsl:element name="a">
							<xsl:attribute name="href">
								<xsl:text>#topFiche</xsl:text>
							</xsl:attribute>
							<xsl:text> </xsl:text>
						</xsl:element>
					</td>
					
				</tr>
			</table>
		</div>

	</xsl:template>
	
	<xsl:template name="changeDate">
		<xsl:param name="laDate"/>
		<xsl:copy-of select="substring($laDate,9,2)"/>-<xsl:copy-of select="substring($laDate,6,2)"/>-<xsl:copy-of select="substring($laDate,1,4)"/><!-- Date textuelle -->
	</xsl:template>

	<xsl:template name="traduire">
		<xsl:param name="texte"/>
		<xsl:if test="count($traductions/langue[@code=$codeLangue]/item[@fr=$texte])>0">
			<xsl:copy-of select="$traductions/langue[@code=$codeLangue]/item[@fr=$texte]/text()"/>
		</xsl:if>
		<xsl:if test="count($traductions/langue[@code=$codeLangue]/item[@fr=$texte])=0">
			<xsl:copy-of select="$texte"/>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="deposeAdresseEtTel">
		<div class="adresse"><xsl:text> </xsl:text>
			<span class="adresse1"><xsl:text> </xsl:text>
				<xsl:copy-of select="/fiche/adresse1/text()"/>
			</span>
			<span class="adresse2"><xsl:text> </xsl:text>
				<xsl:copy-of select="/fiche/adresse2/text()"/>
			</span>
			<span class="adresse3"><xsl:text> </xsl:text>
				<xsl:copy-of select="/fiche/adresse3/text()"/>
			</span>
		</div>
		<div class="cpVille">
			<xsl:copy-of select="/fiche/code_postal/text()"/><xsl:text> </xsl:text>
			<xsl:copy-of select="/fiche/commune/text()"/>
		</div>
		<xsl:if test="string-length(/fiche/telephone1/text())>0">
			<div class="telephone">
				<xsl:call-template name="traduire">
					<xsl:with-param name="texte">Tél : </xsl:with-param>
				</xsl:call-template>
				<xsl:copy-of select="/fiche/telephone1/text()"/>
			</div>
		</xsl:if>
		<xsl:if test="string-length(/fiche/telephone2/text())>0">
			<div class="telephone">
				<xsl:call-template name="traduire">
					<xsl:with-param name="texte">Tél : </xsl:with-param>
				</xsl:call-template>
				<xsl:copy-of select="/fiche/telephone2/text()"/>
			</div>
		</xsl:if>
		<xsl:if test="string-length(/fiche/contact[1]/fax/text())>0">
			<div class="fax">
				<xsl:call-template name="traduire">
					<xsl:with-param name="texte">Fax : </xsl:with-param>
				</xsl:call-template>
				<xsl:copy-of select="/fiche/contact[1]/fax/text()"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="traduireTIF">
		<xsl:param name="codeTIF"/>
		<xsl:copy-of select="php:function('tourismXslTools::traductionTIF',$codeTIF)"/>
	</xsl:template>


	<xsl:template name="getDateRemaniee">
		<xsl:param name="laDate"/>
		<xsl:copy-of select="php:function('tourismXslTools::convertir_date',$laDate)"/>
	</xsl:template>
	
	<xsl:template name="getImageDetailLarge">
		<xsl:param name="srcImage"/>
		<xsl:copy-of select="php:function('pDiffusion::resizeImage', $srcImage, 665, 500)"/>
	</xsl:template>

	<xsl:template name="getImageListe">
		<xsl:param name="srcImage"/>
		<xsl:copy-of select="php:function('pDiffusion::resizeImage', $srcImage, 320, 200)"/>
	</xsl:template>

	<xsl:template name="getImageFormResa">
		<xsl:param name="srcImage"/>
		<xsl:copy-of select="php:function('pDiffusion::resizeImage',$srcImage,'tourism_detail_contact')"/>
	</xsl:template>

	<xsl:template name="getImagePrinkAvis">
		<xsl:param name="srcImage"/>
		<xsl:copy-of select="php:function('pDiffusion::resizeImage',$srcImage,'tourism_detail_princ_avis')"/>
	</xsl:template>

	<xsl:template name="getImageAvis">
		<xsl:param name="srcImage"/>
		<xsl:copy-of select="php:function('pDiffusion::resizeImage',$srcImage,'tourism_detail_avis')"/>
	</xsl:template>

	<xsl:template name="getStaticMap">
		<xsl:param name="gpsLat"/>
		<xsl:param name="gpsLng"/>
		<xsl:copy-of select="php:function('pDiffusion::getStaticMap', $gpsLat, $gpsLng)"/>
	</xsl:template>

	<xsl:template name="coupure-texte">
		<xsl:param name="texte"/>
		<xsl:param name="longueur" select="30"/>
	
		<xsl:choose>
			<xsl:when test="$longueur > string-length($texte)">
				<xsl:value-of select="$texte"/>
			</xsl:when>
			<xsl:when test="substring($texte, $longueur, 1) = ' ' or $texte = ''">
				<xsl:value-of select="concat(substring($texte, 1, $longueur - 1), '...')"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="coupure-texte">
					<xsl:with-param name="texte" select="$texte"/>
					<xsl:with-param name="longueur" select="$longueur - 1"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	

</xsl:stylesheet>