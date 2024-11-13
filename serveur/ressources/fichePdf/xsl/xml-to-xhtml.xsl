<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tif="http://www.tourinfrance.net/Tourinfrance3/"
    xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:php="http://php.net/xsl">
    <xsl:output method="xml" omit-xml-declaration="no"/>
    <!--xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
    </xsl:template-->
	<xsl:template match="/tif:OI">
	
		<xsl:element name="html">
			<xsl:element name="head" />
			<xsl:element name="body">
				<xsl:element name="table">
					<xsl:attribute name="width">100%</xsl:attribute>
					
					<!-- En tete de la fiche (image + adresse) -->
					<xsl:element name="tr">
						<xsl:attribute name="class">header</xsl:attribute>
						
						<xsl:element name="td">
							<xsl:element name="table">
								<xsl:attribute name="cols">110pt 10pt 160pt 10pt 160pt</xsl:attribute>
							
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:attribute name="class">detailPhoto</xsl:attribute>
										
										<xsl:if test="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01']">
											<xsl:element name="img">
												<xsl:attribute name="alt">
													<!-- déposer le nom de la première image -->
													<xsl:value-of select="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01'][1]/tif:Nom"/>
												</xsl:attribute>
												<xsl:attribute name="src">
													<!-- déposer le chemin de la première image -->
													<xsl:value-of select="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01'][1]/tif:URL"/>
												</xsl:attribute>
												<xsl:attribute name="width">100px</xsl:attribute>
											</xsl:element>
										</xsl:if>
									</xsl:element>
									<xsl:element name="td" />
									<xsl:element name="td">
										<xsl:element name="div">
											<xsl:attribute name="class">titre</xsl:attribute>
											
											<!-- déposer la raison sociale -->
											<xsl:element name="b">
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:RaisonSociale"/>
											</xsl:element>
										</xsl:element>
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">street-adress adresse</xsl:attribute>
											
											<xsl:element name="div">
												<xsl:attribute name="class">adresse1</xsl:attribute>
												
												<!-- déposer l'adresse1 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Adr1"/>
											</xsl:element>
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">adresse2</xsl:attribute>
												
												<!-- déposer l'adresse2 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Adr2"/>
											</xsl:element>
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">adresse3</xsl:attribute>
												
												<!-- déposer l'adresse3 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Adr3"/>
											</xsl:element>
										</xsl:element>
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">cpVille</xsl:attribute>
											
											<xsl:element name="span">
												<xsl:attribute name="class">postal-code</xsl:attribute>
												
												<!-- déposer le code postal -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:CodePostal"/>
											</xsl:element>
											<xsl:text> </xsl:text>
											<xsl:element name="span">
												<xsl:attribute name="class">locality</xsl:attribute>
												
												<!-- déposer la commune -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Commune"/>
											</xsl:element>
										</xsl:element>
										<xsl:if test="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.01'][1]/tif:Coord">
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">tel telephone</xsl:attribute>
											
											<xsl:text>Téléphone : </xsl:text>
											<!-- déposer le téléphone -->
											<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.01'][1]/tif:Coord"/>
										</xsl:element>
										</xsl:if>
										<xsl:if test="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.02'][1]/tif:Coord">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">tel telephone</xsl:attribute>
												
												<xsl:text>Fax : </xsl:text>
												<!-- déposer le fax -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.02'][1]/tif:Coord"/>
											</xsl:element>
										</xsl:if>
										<xsl:if test="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.04'][1]/tif:Coord">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">tel telephone</xsl:attribute>
												
												<xsl:text>Mail : </xsl:text>
												<!-- déposer le mail -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.04'][1]/tif:Coord"/>
											</xsl:element>
										</xsl:if>
										<xsl:if test="string-length(//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord/text())&gt;0">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">LienSiteWeb url</xsl:attribute>
												
												<xsl:element name="a">
													<xsl:attribute name="target">_blank</xsl:attribute>
													<xsl:attribute name="href">
														<!-- déposer le site web -->
														<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord"/>
													</xsl:attribute>
													
													<xsl:value-of select="//tif:DetailContact[@type='04.03.13']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord"/>
												</xsl:element>
											</xsl:element>
										</xsl:if>
									</xsl:element>
									<xsl:element name="td" />
									<xsl:element name="td">
										<xsl:element name="div">
											<xsl:attribute name="class">titre</xsl:attribute>
											
											<!-- déposer la raison sociale -->
											<xsl:text>Propriétaire</xsl:text>
										</xsl:element>
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">street-adress adresse</xsl:attribute>
											
											<xsl:element name="div">
												<xsl:attribute name="class">adresse1</xsl:attribute>
												
												<!-- déposer l'adresse1 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Adr1"/>
											</xsl:element>
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">adresse2</xsl:attribute>
												
												<!-- déposer l'adresse2 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Adr2"/>
											</xsl:element>
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">adresse3</xsl:attribute>
												
												<!-- déposer l'adresse3 -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Adr3"/>
											</xsl:element>
										</xsl:element>
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">cpVille</xsl:attribute>
											
											<xsl:element name="span">
												<xsl:attribute name="class">postal-code</xsl:attribute>
												
												<!-- déposer le code postal -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:CodePostal"/>
											</xsl:element>
											<xsl:text> </xsl:text>
											<xsl:element name="span">
												<xsl:attribute name="class">locality</xsl:attribute>
												
												<!-- déposer la commune -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Commune"/>
											</xsl:element>
										</xsl:element>
										<xsl:if test="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.01'][1]/tif:Coord">
										<xsl:element name="br" />
										<xsl:element name="div">
											<xsl:attribute name="class">tel telephone</xsl:attribute>
											
											<xsl:text>Téléphone : </xsl:text>
											<!-- déposer le téléphone -->
											<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.01'][1]/tif:Coord"/>
										</xsl:element>
										</xsl:if>
										<xsl:if test="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.02'][1]/tif:Coord">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">tel telephone</xsl:attribute>
												
												<xsl:text>Fax : </xsl:text>
												<!-- déposer le fax -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.02'][1]/tif:Coord"/>
											</xsl:element>
										</xsl:if>
										<xsl:if test="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.04'][1]/tif:Coord">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">tel telephone</xsl:attribute>
												
												<xsl:text>Mail : </xsl:text>
												<!-- déposer le mail -->
												<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.04'][1]/tif:Coord"/>
											</xsl:element>
										</xsl:if>
										<xsl:if test="string-length(//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord/text())&gt;0">
											<xsl:element name="br" />
											<xsl:element name="div">
												<xsl:attribute name="class">LienSiteWeb url</xsl:attribute>
												
												<xsl:element name="a">
													<xsl:attribute name="target">_blank</xsl:attribute>
													<xsl:attribute name="href">
														<!-- déposer le site web -->
														<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord"/>
													</xsl:attribute>
													
													<xsl:value-of select="//tif:DetailContact[@type='04.03.30']/tif:Adresses/tif:DetailAdresse/tif:Personnes/tif:DetailPersonne[@type='04.04.05'][1]/tif:MoyensCommunications/tif:DetailMoyenCom[@type='04.02.05'][1]/tif:Coord"/>
												</xsl:element>
											</xsl:element>
										</xsl:if>
									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:element>


					<!-- Description commerciale -->
					<xsl:element name="tr">
						<xsl:attribute name="class">description</xsl:attribute>

						<xsl:element name="td">
							<xsl:element name="table">
								<xsl:element name="tr">
									<xsl:attribute name="height">10px</xsl:attribute>
									<xsl:element name="td" />
								</xsl:element>
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:element name="div">
											<!-- déposer la description commerciale -->
											<xsl:element name="br" />
											<xsl:value-of select="tif:DublinCore/dc:description[@xml:lang='fr'][1]"/>
										</xsl:element>
									</xsl:element>
								</xsl:element>
								<xsl:element name="tr">
									<xsl:attribute name="height">12px</xsl:attribute>
									<xsl:element name="td" />
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:element>
					
					<!-- Coordonnées GPS -->
					<xsl:element name="tr">
						<xsl:attribute name="class">GPS</xsl:attribute>

						<xsl:element name="td">
							<xsl:element name="table">
								<xsl:element name="tr">
									<xsl:attribute name="height">10px</xsl:attribute>
									<xsl:element name="td" />
								</xsl:element>
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:element name="div">
											<!-- déposer la latitude -->
											<xsl:element name="br" />
											<xsl:text>Latitude : </xsl:text>
											<xsl:value-of select="//tif:Latitude[1]"/>
										</xsl:element>
									</xsl:element>
								</xsl:element>
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:element name="div">
											<!-- déposer la longitude -->
											<xsl:element name="br" />
											<xsl:text>Longitude : </xsl:text>
											<xsl:value-of select="//tif:Longitude[1]"/>
										</xsl:element>
									</xsl:element>
								</xsl:element>
								<xsl:element name="tr">
									<xsl:attribute name="height">12px</xsl:attribute>
									<xsl:element name="td" />
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:element>
					
					
					<!-- Langues parlées et logo -->
					<xsl:element name="tr">
						<xsl:attribute name="class">logoLangues</xsl:attribute>

						<xsl:element name="td">
							<xsl:element name="table">
								<xsl:attribute name="cols">110pt 10pt 330pt</xsl:attribute>
								
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:attribute name="class">langues</xsl:attribute>
										<xsl:attribute name="width">20%</xsl:attribute>
										<xsl:attribute name="height">70px</xsl:attribute>
										
										<xsl:element name="fieldset">
											<xsl:attribute name="height">50pt</xsl:attribute>
											
											<xsl:element name="legend">Langues parlées</xsl:element>
											<xsl:element name="div">
												<xsl:attribute name="class">langues</xsl:attribute>
												
												<xsl:if test="tif:Langues/tif:Usage[@type='11.01.01']/tif:Langue">
													<xsl:element name="table">
														<xsl:attribute name="cols">
															<!-- Compter le nombre de langues, et rajouter un "30pt " pour chaque ici -->
															<xsl:for-each select="tif:Langues/tif:Usage[@type='11.01.01']/tif:Langue">
																<xsl:text>20pt </xsl:text>
															</xsl:for-each>
														</xsl:attribute>
														
														<xsl:element name="tr">
															<!-- pour chaque langue -->
															<xsl:for-each select="tif:Langues/tif:Usage[@type='11.01.01']/tif:Langue">
																<xsl:element name="td">
																	<xsl:element name="img">
																		<xsl:attribute name="alt">
																			<!-- déposer le code langue -->
																			<xsl:value-of select="@lang"/>
																		</xsl:attribute>
																		<xsl:attribute name="src">
																			<xsl:text>http://www.royan-tourisme.com/sites/all/modules/_raccourci/tourism_raccourci/ressources/images/drapeaux/44/</xsl:text>
																			<!-- déposer le code langue -->
																			<xsl:value-of select="@lang"/>
																			<xsl:text>.png</xsl:text>
																		</xsl:attribute>
																	</xsl:element>
																</xsl:element>
															</xsl:for-each>
														</xsl:element>
													</xsl:element>
												</xsl:if>
											</xsl:element>
										</xsl:element>
									</xsl:element>
									<xsl:element name="td" />
									<xsl:element name="td">
										<xsl:attribute name="class">detailLogo</xsl:attribute>
										<xsl:attribute name="width">80%</xsl:attribute>
										<xsl:attribute name="height">70px</xsl:attribute>
										
										<xsl:if test="tif:Classements/tif:DetailClassement">
											<xsl:element name="fieldset">
												<xsl:attribute name="height">50pt</xsl:attribute>
												
												<xsl:element name="legend">Labels</xsl:element>
												<xsl:element name="div">
													<xsl:attribute name="class">detailLabel</xsl:attribute>
													
													<xsl:element name="div">
														<xsl:element name="table">
															<xsl:element name="tr">
																<!-- pour chaque label -->
																<xsl:for-each select="tif:Classements/tif:DetailClassement">
																	<xsl:if test="tif:Classement">
																		<xsl:for-each select="tif:Classement">
																			<xsl:element name="td">
																				<xsl:element name="div">
																					<!-- Déposer ici la traduction du code TIF du label -->
																					<xsl:call-template name="traduireTIF">
																						<xsl:with-param name="codeTIF">
																							<xsl:value-of select="@type"/><!-- php>tif -->
																						</xsl:with-param>
																					</xsl:call-template>
																				</xsl:element>
																			</xsl:element>
																		</xsl:for-each>
																	</xsl:if>
																	<xsl:if test="not(tif:Classement)">
																		<xsl:element name="td">
																			<xsl:element name="div">
																				<!-- Déposer ici la traduction du code TIF du label -->
																				<xsl:call-template name="traduireTIF">
																					<xsl:with-param name="codeTIF">
																						<xsl:value-of select="@type"/><!-- php>tif -->
																					</xsl:with-param>
																				</xsl:call-template>
																			</xsl:element>
																		</xsl:element>
																	</xsl:if>
																</xsl:for-each>
															</xsl:element>
														</xsl:element>
														
													</xsl:element>
												</xsl:element>
											</xsl:element>
										</xsl:if>
									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:element>
					
					
					<!-- Images -->
					<xsl:if test="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01']">
						<xsl:element name="tr">
							<xsl:attribute name="class">galleryPhoto</xsl:attribute>

							<xsl:element name="td">
								<xsl:element name="table">
									<xsl:attribute name="cols">390pt 390pt 390pt</xsl:attribute>
									
									<xsl:element name="tr">
										<xsl:element name="td">
											<xsl:element name="h2">Photos</xsl:element>
											<xsl:element name="table">
												<xsl:element name="tr">
													<!-- pour chacune des 3 premières images -->
													<xsl:for-each select="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01' and position()&lt;4]">
														<xsl:element name="td">
															<xsl:element name="img">
																<xsl:attribute name="alt">
																	<!-- déposer le nom de l'image -->
																	<xsl:value-of select="tif:Nom"/>
																</xsl:attribute>
																<xsl:attribute name="src">
																	<!-- déposer le chemin de l'image -->
																	<xsl:value-of select="tif:URL"/>
																</xsl:attribute>
																<xsl:attribute name="width">120px</xsl:attribute>
																<xsl:attribute name="height">90px</xsl:attribute>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
												<xsl:if test="count(tif:Multimedia/tif:DetailMultimedia[@type='03.01.01'])&gt;3">
												<xsl:element name="tr">
													<!-- pour chacune des 3 premières images -->
													<xsl:for-each select="tif:Multimedia/tif:DetailMultimedia[@type='03.01.01' and position()&gt;3 and position()&lt;7]">
														<xsl:element name="td">
															<xsl:element name="img">
																<xsl:attribute name="alt">
																	<!-- déposer le nom de l'image -->
																	<xsl:value-of select="tif:Nom"/>
																</xsl:attribute>
																<xsl:attribute name="src">
																	<!-- déposer le chemin de l'image -->
																	<xsl:value-of select="tif:URL"/>
																</xsl:attribute>
																<xsl:attribute name="width">120px</xsl:attribute>
																<xsl:attribute name="height">90px</xsl:attribute>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
												</xsl:if>
											</xsl:element>
										</xsl:element>
									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:if>
					
					<!-- Périodes d'ouverture et prestations -->
					<xsl:element name="tr">
						<xsl:attribute name="class">prestations</xsl:attribute>

						<xsl:element name="td">
							<xsl:element name="table">
								<xsl:element name="tr">
									<xsl:element name="td">
										<xsl:element name="h2">Prestations</xsl:element>
										<xsl:if test="tif:Periodes/tif:DetailPeriode[@type='09.01.01']">
											<xsl:element name="fieldset">
												<xsl:attribute name="class">periode</xsl:attribute>
												
												<xsl:element name="legend">Période(s) d'ouverture :</xsl:element>
												<xsl:element name="ul">
													<!-- pour chaque période d'ouverture -->
													<xsl:for-each select="tif:Periodes/tif:DetailPeriode[@type='09.01.01']">
														<xsl:element name="li">
															<xsl:element name="div">
																<xsl:text>du </xsl:text>
																<!-- déposer ici la période d'ouverture début -->
																<xsl:call-template name="getDateRemaniee">
																	<xsl:with-param name="laDate">
																		<xsl:value-of select="tif:Dates/tif:DetailDates/tif:DateDebut"/>
																	</xsl:with-param>
																</xsl:call-template>
																<xsl:text> au </xsl:text>
																<!-- déposer ici la période d'ouverture fin -->
																<xsl:call-template name="getDateRemaniee">
																	<xsl:with-param name="laDate">
																		<xsl:value-of select="tif:Dates/tif:DetailDates/tif:DateFin"/>
																	</xsl:with-param>
																</xsl:call-template>
															</xsl:element>
															<xsl:if test="string-length(tif:ObservationDetailPeriode[@xml:lang='fr']/text())>0">
																<xsl:element name="br" />
																<xsl:element name="span">
																	<xsl:attribute name="class">descriptionOuverture</xsl:attribute>
																	
																	<!-- déposer ici la description de la période -->
																	<xsl:value-of select="tif:ObservationDetailPeriode[@xml:lang='fr']"/>
																</xsl:element>
															</xsl:if>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:if>

										<xsl:if test="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.03']/tif:DetailPrestation">
											<xsl:element name="fieldset">
												<xsl:attribute name="class">confort</xsl:attribute>
												
												<xsl:element name="legend">Conforts</xsl:element>

												<xsl:element name="table">
													<!-- pour chacune des lignes de conforts -->
													<xsl:for-each select="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.03']/tif:DetailPrestation[position() mod 3 = 1]">
															
														<xsl:element name="tr">
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																			<xsl:value-of select="tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:variable name="tmpVar">
																<xsl:value-of select="tif:Prestation/@type"/>
															</xsl:variable>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:if>

										<xsl:if test="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.05']/tif:DetailPrestation">
											<xsl:element name="fieldset">
												<xsl:attribute name="class">equipement</xsl:attribute>
												
												<xsl:element name="legend">Équipements</xsl:element>
												<xsl:element name="table">
													<xsl:for-each select="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.05']/tif:DetailPrestation[position() mod 3 = 1]">
															
														<xsl:element name="tr">
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																			<xsl:value-of select="tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:variable name="tmpVar">
																<xsl:value-of select="tif:Prestation/@type"/>
															</xsl:variable>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:if>

										<xsl:if test="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.06']/tif:DetailPrestation">
											<xsl:element name="fieldset">
												<xsl:attribute name="class">service</xsl:attribute>
												
												<xsl:element name="legend">Services</xsl:element>
												<xsl:element name="table">
													<xsl:for-each select="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.06']/tif:DetailPrestation[position() mod 3 = 1]">
															
														<xsl:element name="tr">
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																			<xsl:value-of select="tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:variable name="tmpVar">
																<xsl:value-of select="tif:Prestation/@type"/>
															</xsl:variable>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																</xsl:element>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:if>

										<xsl:if test="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.02']/tif:DetailPrestation">
											<xsl:element name="fieldset">
												<xsl:attribute name="class">activite</xsl:attribute>
												
												<xsl:element name="legend">Activités</xsl:element>
												<xsl:element name="table">
													<xsl:for-each select="tif:OffresPrestations/tif:DetailOffrePrestation[attribute::type='15.02']/tif:DetailPrestation[position() mod 3 = 1]">
															
														<xsl:element name="tr">
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																			<xsl:value-of select="tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																	<xsl:if test="tif:Distance">
																		<xsl:text> (</xsl:text>
																		<!-- déposer ici la distance de l'activité -->
																		<xsl:value-of select="tif:Distance"/>
																		<xsl:text> </xsl:text>
																		<!-- déposer ici l'unité de la distance de l'activité -->
																		<xsl:call-template name="traduireTIF">
																			<xsl:with-param name="codeTIF">
																				<xsl:value-of select="tif:Distance/@unite"/><!-- php>tif -->
																			</xsl:with-param>
																		</xsl:call-template>
																		<xsl:text>)</xsl:text>
																	</xsl:if>
																</xsl:element>
															</xsl:element>
															<xsl:variable name="tmpVar">
																<xsl:value-of select="tif:Prestation/@type"/>
															</xsl:variable>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																	<!-- xsl:if test="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Distance"-->
																		<!--xsl:text> (</xsl:text-->
																		<!-- déposer ici la distance de l'activité -->
																		<!--xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Distance"/-->
																		<!--xsl:text> </xsl:text-->
																		<!-- déposer ici l'unité de la distance de l'activité -->
																		<!--xsl:call-template name="traduireTIF"-->
																			<!-- xsl:with-param name="codeTIF"-->
																				<!--xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/tif:Distance/@unite"/--><!-- php>tif -->
																			<!--/xsl:with-param-->
																		<!--/xsl:call-template-->
																		<!--xsl:text>)</xsl:text-->
																	<!--/xsl:if-->
																</xsl:element>
															</xsl:element>
															<xsl:element name="td">
																<xsl:element name="div">
																	<xsl:text>- </xsl:text>
																	<!-- déposer ici la traduction du code TIF du confort -->
																	<xsl:call-template name="traduireTIF">
																		<xsl:with-param name="codeTIF">
																		
																			<xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Prestation/@type"/><!-- php>tif -->
																		</xsl:with-param>
																	</xsl:call-template>
																	<!--xsl:if test="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Distance"-->
																		<!--xsl:text> (</xsl:text-->
																		<!-- déposer ici la distance de l'activité -->
																		<!--xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Distance"/-->
																		<!--xsl:text> </xsl:text-->
																		<!-- déposer ici l'unité de la distance de l'activité -->
																		<!--xsl:call-template name="traduireTIF"-->
																			<!--xsl:with-param name="codeTIF"-->
																				<!--xsl:value-of select="../tif:DetailPrestation[tif:Prestation/@type=$tmpVar]/following-sibling::tif:DetailPrestation/following-sibling::tif:DetailPrestation/tif:Distance/@unite"/--><!-- php>tif -->
																			<!--/xsl:with-param-->
																		<!--/xsl:call-template-->
																		<!--xsl:text>)</xsl:text-->
																	<!--/xsl:if-->
																</xsl:element>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:if>

									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:element>

					
					<!-- Tarifs -->
					<xsl:if test="tif:Tarifs/tif:DetailTarifs/tif:DetailTarif">
						<xsl:element name="tr">
							<xsl:attribute name="class">tarif</xsl:attribute>

							<xsl:element name="td">
								<xsl:element name="table">
									<xsl:attribute name="width">100%</xsl:attribute>
									
									<xsl:element name="tr">
										<xsl:element name="td">
											<xsl:element name="h2">Tarifs</xsl:element>
											<xsl:element name="table">
												<xsl:attribute name="cellspacing">0</xsl:attribute>
												<xsl:attribute name="width">100%</xsl:attribute>
												<xsl:attribute name="border">1</xsl:attribute>
												
												<xsl:element name="tr">
													<xsl:element name="th">
														<xsl:attribute name="class">libelle</xsl:attribute>
														
														<xsl:element name="div">
															<xsl:text>Tarifs</xsl:text>
														</xsl:element>
													</xsl:element>
													<xsl:element name="th">
														<xsl:attribute name="class">min</xsl:attribute>
														
														<xsl:element name="div">
															<xsl:text>Min</xsl:text>
														</xsl:element>
													</xsl:element>
													<xsl:element name="th">
														<xsl:attribute name="class">max</xsl:attribute>
														
														<xsl:element name="div">
															<xsl:text>Max</xsl:text>
														</xsl:element>
													</xsl:element>
													<xsl:element name="th">
														<xsl:attribute name="class">description</xsl:attribute>
														
														<xsl:element name="div">
															<xsl:text>Description</xsl:text>
														</xsl:element>
													</xsl:element>
												</xsl:element>
												<!-- pour chaque tarif -->
												<xsl:for-each select="tif:Tarifs/tif:DetailTarifs/tif:DetailTarif">
													<xsl:element name="tr">
														<xsl:attribute name="class">
															<xsl:choose>
																<!-- si tarif est à une position impaire, déposer "ligne1"-->
																<xsl:when test="(position() mod 2)=0">
																	<xsl:text>ligne1</xsl:text>
																</xsl:when>
																<!-- si tarif est à une position paire, déposer "ligne2"-->
																<xsl:otherwise>
																	<xsl:text>ligne2</xsl:text>
																</xsl:otherwise>
															</xsl:choose>
															<!-- si tarif est à la dernière position, déposer " last"-->
															<xsl:if test="position()=count(//tif:DetailTarif)">
																<xsl:text> last</xsl:text>
															</xsl:if>
															<!-- si tarif est à la première position, déposer " first"-->
															<xsl:if test="position()=1">
																<xsl:text> first</xsl:text>
															</xsl:if>
														</xsl:attribute>
														<!-- si tarif est à une position paire -->
														<xsl:if test="(position() mod 2)=0">
															<xsl:attribute name="style">background:#F3F3F3;</xsl:attribute>
														</xsl:if>
														
														<xsl:element name="td">
															<xsl:attribute name="class">libelle</xsl:attribute>
															
															<xsl:element name="div">
																<!-- Déposer ici la traduction du code TIF du tarif -->
																<xsl:call-template name="traduireTIF">
																	<xsl:with-param name="codeTIF">
																		<xsl:value-of select="@type"/><!-- php>tif -->
																	</xsl:with-param>
																</xsl:call-template>
															</xsl:element>
														</xsl:element>
														<!-- si tarifmin = tarifmax ou tarifmax est absent -->
														<xsl:if test="tif:TarifStandard">
															<xsl:element name="td">
																<xsl:attribute name="class">minmax</xsl:attribute>
																<xsl:attribute name="align">center</xsl:attribute>
																<xsl:attribute name="colspan">2</xsl:attribute>
																
																<xsl:element name="div">
																	<!-- Déposer ici le tarif min -->
																	<xsl:value-of select="tif:TarifStandard"/>
																	<xsl:text> €</xsl:text>
																</xsl:element>
															</xsl:element>
														</xsl:if>
														<!-- fin si -->
														<!-- si tarifmax != tarifmin -->
														<xsl:if test="not(tif:TarifStandard)">
															<xsl:element name="td">
																<xsl:attribute name="class">min</xsl:attribute>
																
																<xsl:element name="div">
																	<!-- Déposer ici le tarif min -->
																	<xsl:value-of select="tif:TarifMin"/>
																	<xsl:text> €</xsl:text>
																</xsl:element>
															</xsl:element>
															<xsl:element name="td">
																<xsl:attribute name="class">max</xsl:attribute>
																
																<xsl:element name="div">
																	<!-- Déposer ici le tarif max -->
																	<xsl:value-of select="tif:TarifMax"/>
																	<xsl:text> €</xsl:text>
																</xsl:element>
															</xsl:element>
														</xsl:if>
														<!-- fin si -->
														<xsl:element name="td">
															<xsl:attribute name="class">description</xsl:attribute>
															
															<xsl:element name="div">
																<!-- Déposer ici la description du tarif -->
																<xsl:value-of select="tif:DescriptionTarif"/>
															</xsl:element>
														</xsl:element>
													</xsl:element>
												</xsl:for-each>
											</xsl:element>
										</xsl:element>
									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:if>

					
					<!-- Modes de paiement -->
					<xsl:if test="tif:Tarifs/tif:ModesPaiement/tif:ModePaiement">
						<xsl:element name="tr">
							<xsl:attribute name="class">paiement</xsl:attribute>

							<xsl:element name="td">
								<xsl:element name="table">
									<xsl:element name="tr">
										<xsl:element name="td">
											<xsl:element name="h2">Mode(s) de paiement</xsl:element>
											<xsl:element name="table">
												<xsl:element name="tr">
													<!-- pour chaque mode de paiement -->
													<xsl:for-each select="tif:Tarifs/tif:ModesPaiement/tif:ModePaiement">
														<xsl:element name="td">
															<xsl:element name="div">
																<!-- Déposer ici la traduction du code TIF du mode de paiement -->
																<xsl:call-template name="traduireTIF">
																	<xsl:with-param name="codeTIF">
																		<xsl:value-of select="@type"/><!-- php>tif -->
																	</xsl:with-param>
																</xsl:call-template>
															</xsl:element>
														</xsl:element>
													</xsl:for-each>
												</xsl:element>
											</xsl:element>
										</xsl:element>
									</xsl:element>
								</xsl:element>
							</xsl:element>
						</xsl:element>
					</xsl:if>

				</xsl:element>
			</xsl:element>
		</xsl:element>
	</xsl:template>
	
	<xsl:template name="traduireTIF">
		<xsl:param name="codeTIF"/>
		<xsl:copy-of select="php:function('xslFunctions::traduireTIF',$codeTIF)"/>
	</xsl:template>
	
	<xsl:template name="getDateRemaniee">
		<xsl:param name="laDate"/>
		<xsl:copy-of select="php:function('xslFunctions::convertirDate',$laDate)"/>
	</xsl:template>
</xsl:stylesheet>