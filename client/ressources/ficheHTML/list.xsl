<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" omit-xml-declaration="yes" indent="yes" encoding="UTF-8" version="1.0" />
	
	<xsl:param name="codeLangue"/>
	
	<xsl:variable name="traductions" select="document('translation.xml')/traductions"/>
	
	<xsl:template match="/fiche">
		<xsl:variable name="idFiche" select="/fiche/idFiche/text()"/>
		<xsl:variable name="raisonSociale" select="/fiche/raison_sociale/text()"/>
		
		<div class="listeHeader">
			<div class="listeTitre">
				<xsl:copy-of select="raison_sociale/text()"/>
			</div>
			<div class="listeClassement"><xsl:text> </xsl:text>
				<xsl:for-each select="classement">
					<xsl:call-template name="traduireTIFspan">
						<xsl:with-param name="codeTIF">
							<xsl:copy-of select="./text()"/><!-- appeler la methode php qui traduit les champs tif -->
						</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</div>
		</div>
		<xsl:if test="count(/fiche/chaine/cle)>0">
			<xsl:if test="/fiche/chaine/cle/text()!='100.06.06.02.03'"><!-- verif crad fleur de soleil -->
			<div class="listeLogoPrincipal">
				<span class="left"><xsl:text> </xsl:text></span>
				<span class="middle">
					<xsl:call-template name="traduireTIF">
						<xsl:with-param name="codeTIF">
							<xsl:copy-of select="/fiche/chaine[1]/cle/text()"/><!-- php>tif -->
						</xsl:with-param>
					</xsl:call-template><xsl:text> </xsl:text>
				</span>
				<span class="right"><xsl:text> </xsl:text></span>
			</div>
			</xsl:if>
		</xsl:if>
		<div class="listePhoto">
			<xsl:for-each select="/fiche/fonctionnalites">
				<xsl:if test="text()='photoModeListe'">
					<xsl:if test="count(/fiche/fonctionnalites[text()='modeDetail'])=1">
						<xsl:element name="a">
							<xsl:attribute name="href">
								<xsl:copy-of select="$urlDetail"/>
							</xsl:attribute>
							<!--xsl:attribute name="onclick">
								<xsl:text>javascript:return false;</xsl:text>
							</xsl:attribute-->
							<xsl:attribute name="class">
								<xsl:text>lienDetail</xsl:text>
							</xsl:attribute>
							<xsl:choose>
								<xsl:when test="count(/fiche/photos_fichiers[1]/url_fichier)>=1">
									<!--xsl:element name="img">
										<xsl:attribute name="src">
											<xsl:call-template name="getImageListe">
												<xsl:with-param name="srcImage">
													<xsl:copy-of select="/fiche/photos_fichiers[1]/url_fichier/text()"/>
												</xsl:with-param>
											</xsl:call-template>
										</xsl:attribute>
										<xsl:attribute name="alt"><xsl:copy-of select="/fiche/photos_fichiers[1]/nom_fichier/text()"/></xsl:attribute>
									</xsl:element-->
									<div class="imageToBeReplaced"><xsl:text> </xsl:text></div>
								</xsl:when>
								<!--xsl:otherwise>
									<img src="/sites/all/modules/_raccourci/tourism_raccourci/images/visuel_defaut.jpg" />
								</xsl:otherwise-->
							</xsl:choose>
							<xsl:text> </xsl:text>
						</xsl:element>
					</xsl:if>
					<xsl:if test="count(/fiche/fonctionnalites[text()='modeDetail'])=0">
						<xsl:choose>
							<xsl:when test="count(../photos_fichiers[1]/url_fichier)>=1">
								<!--xsl:element name="img">
									<xsl:attribute name="src">
										<xsl:call-template name="getImageListe">
											<xsl:with-param name="srcImage">
												<xsl:copy-of select="../photos_fichiers[1]/url_fichier/text()"/>
											</xsl:with-param>
										</xsl:call-template>
									</xsl:attribute>
									<xsl:attribute name="alt"><xsl:copy-of select="../photos_fichiers[1]/nom_fichier/text()"/></xsl:attribute>
								</xsl:element-->
								<div class="imageToBeReplaced"><xsl:text> </xsl:text></div>
							</xsl:when>
							<!--xsl:otherwise>
								<img src="/sites/all/modules/_raccourci/tourism_raccourci/images/visuel_defaut.jpg" />
							</xsl:otherwise-->
						</xsl:choose>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
			<xsl:text> </xsl:text>
		</div>
		<xsl:element name="div">
			<xsl:attribute name="class">
				<xsl:text>listeContent</xsl:text>
				<xsl:if test="count(/fiche/photos_fichiers[1]/url_fichier)=0">
					<xsl:if test="count(/fiche/chaine/cle)>0">
						<xsl:text> noPhotoWithLogo</xsl:text>
					</xsl:if>
				</xsl:if>
			</xsl:attribute>
			<xsl:for-each select="fonctionnalites">
				<xsl:if test="text()='modeDetail'">
					<div class="listeLogo">
						<ul>
						<xsl:for-each select="fonctionnalites">
							<xsl:if test="text()='visitesVirtuelles'">
								<li>
									<img src="/sites/all/modules/_raccourci/tourism_raccourci/images/ico_360.png" alt="360°C" title="360°C" />
								</li>
							</xsl:if>
						</xsl:for-each>
						
						<xsl:if test="count(/fiche/photos_fichiers[1]/url_fichier)>=1">
							<li>
								<xsl:element name="a">
									<xsl:attribute name="href">
										<xsl:copy-of select="concat($urlDetail,'#photos')"/>
									</xsl:attribute>
									<!--xsl:attribute name="onclick">
										<xsl:text>javascript:return false;</xsl:text>
									</xsl:attribute-->
									<xsl:attribute name="class">
										<xsl:text>lienDetail</xsl:text>
									</xsl:attribute>
									<img src="/sites/all/modules/_raccourci/tourism_raccourci/images/ico_photo.png" alt="Photos" title="Photos" />
								</xsl:element>
							</li>
						</xsl:if>
						<xsl:for-each select="fonctionnalites">
							<xsl:if test="text()='videos'">
								<li>
									<img src="/sites/all/modules/_raccourci/tourism_raccourci/images/video.png" alt="Vidéos" title="Vidéos" />
								</li>
							</xsl:if>
						</xsl:for-each>
						</ul>
					</div>
				</xsl:if>
			</xsl:for-each>
			<xsl:if test="count(/fiche/fonctionnalites[text()='avisInternautes'])=1">
				<xsl:if test="count(/fiche/avis/liste/avis_item)>0">
					<div class="listeAvis">
						<xsl:element name="a">
							<xsl:attribute name="href">
								<xsl:copy-of select="concat($urlDetail,'#avis')"/>
							</xsl:attribute>
							<!--xsl:attribute name="onclick">
								<xsl:text>javascript:return false;</xsl:text>
							</xsl:attribute-->
							<xsl:attribute name="class">
								<xsl:text>lienDetail</xsl:text>
							</xsl:attribute>
							<xsl:element name="img">
								<xsl:attribute name="src">
									<xsl:text>/sites/all/modules/_raccourci/tourism_raccourci/isc/images/avis/20/smiley_liste_</xsl:text>
									<xsl:copy-of select="/fiche/avis/general/moyenne/text()"/>
									<xsl:text>.png</xsl:text>
								</xsl:attribute>
							</xsl:element>
							<span class="labelAvis">
								<xsl:copy-of select="count(/fiche/avis/liste/avis_item)"/>
								<xsl:text> avis</xsl:text>
							</span>
						</xsl:element>
					</div>
				</xsl:if>
			</xsl:if>
			<xsl:if test="count(/fiche/fonctionnalites[text()='descriptionListe'])=1">
				<xsl:element name="div">
					<xsl:attribute name="class">
						<xsl:text>listeResume</xsl:text>
					</xsl:attribute>
					<xsl:call-template name="coupure-texte">
						<xsl:with-param name="texte" select="description_commerciale_fr/text()"/>
						<xsl:with-param name="longueur" select="200"/>						
					</xsl:call-template>
					<xsl:text> </xsl:text>
				</xsl:element>
			</xsl:if>
			<xsl:if test="count(/fiche/fonctionnalites[text()='modeDetail'])=0">
				<xsl:if test="count(/fiche/fonctionnalites[text()='adresseModeListe'])=1">
					<span>
						<xsl:call-template name="traduire">
							<xsl:with-param name="texte">Adresse</xsl:with-param>
						</xsl:call-template>
					</span>
					<xsl:call-template name="deposeAdresseEtTel"/>
				</xsl:if>
			</xsl:if>
			<div class="listeBoutons">
				<ul>
					<xsl:for-each select="fonctionnalites">
						<xsl:if test="text()='modeDetail'">
							<li class="listeBoutonDetails">
								<xsl:element name="a">
									<xsl:attribute name="href">
										<xsl:copy-of select="$urlDetail"/>
									</xsl:attribute>
									<!--xsl:attribute name="onclick">
										<xsl:text>javascript:return false;</xsl:text>
									</xsl:attribute-->
									<xsl:attribute name="class">
										<xsl:text>lienDetail</xsl:text>
									</xsl:attribute>
									<span class="left"><xsl:text> </xsl:text></span>
									<span class="middle">
										<xsl:call-template name="traduire">
											<xsl:with-param name="texte">Détails</xsl:with-param>
										</xsl:call-template>
									</span>
									<span class="right"><xsl:text> </xsl:text></span>
									
								</xsl:element>
							</li>
						</xsl:if>
					</xsl:for-each>

					<li class="listeBoutonCarnetVoyage">
						<xsl:attribute name="id">
							<xsl:copy-of select="$idFiche"/>
						</xsl:attribute>
						<xsl:call-template name="traduire">
							<xsl:with-param name="texte">Ajouter au carnet de voyage</xsl:with-param>
						</xsl:call-template>
					</li>
					<xsl:if test="/fiche/cs_urlresa/text()!=''">
						<xsl:for-each select="/fiche/fonctionnalites">
							<xsl:if test="text()='lienReservation'">
								<li class="listeBoutonReserver">
									<xsl:element name="a">
										<xsl:attribute name="href">
											<xsl:copy-of select="/fiche/cs_urlresa/text()"/>
										</xsl:attribute>
										<xsl:attribute name="class">
											<xsl:text>reserverliste</xsl:text>
										</xsl:attribute>
										<span class="left"><xsl:text> </xsl:text></span>
										<span class="middle">
											<xsl:call-template name="traduire">
												<xsl:with-param name="texte">Réserver</xsl:with-param>
											</xsl:call-template>
										</span>
										<span class="right"><xsl:text> </xsl:text></span>
									</xsl:element>
								</li>
							</xsl:if>
						</xsl:for-each>
					</xsl:if>
				</ul>
			</div>
		</xsl:element>
		
	</xsl:template>
	
	<xsl:include href="templates.xsl"/>
	
</xsl:stylesheet>