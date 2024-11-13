jsGallery = function(idContainer, params){this.init(idContainer, params);return this;}

jsGallery.prototype.init = function(idContainer, params)
{
	
	/**
	 * Configuration de la galerie
	 */
		
		this.cols = (params['cols']) ? parseInt(params['cols']) : 3;										// Nombre de colonnes
		this.rows = (params['rows']) ? parseInt(params['rows']) : 4;										// Nombre de lignes
		this.transitionDelay = (params['transitionDelay']) ? parseInt(params['transitionDelay']) : 20;		// Délai de transition pour le fondu
		this.deltaOpacity = (params['deltaOpacity']) ? parseFloat(params['deltaOpacity']) : 0.15;			// Delta de l'opacité entre chaque transition
		this.thumbOpacityOff = (params['thumbOpacityOff']) ? parseFloat(params['thumbOpacityOff']) : 0.5;	// Opacité d'une vignette inactive
		this.borderColorOn = (params['borderColorOn']) ? params['borderColorOn'] : '#666';					// Couleur de la bordure d'une vignette active
		this.borderColorOff = (params['borderColorOff']) ? params['borderColorOff'] : '#ccc';				// Couleur de la bordure d'une vignette inactive

		this.imgSuivantImage = (params['imgSuivantImage']) ? params['imgSuivantImage'] : '/ressources/ficheHTML/jsGallery/suivant.png';		// Url de l'image de navigation suivante (sur l'image)
		this.imgPrecedentImage = (params['imgPrecedentImage']) ? params['imgPrecedentImage'] : '/ressources/ficheHTML/jsGallery/precedent.png';// Url de l'image de navigation précédente (sur l'image)
		this.imgArrowWidth = (params['imgArrowWidth']) ? parseInt(params['imgArrowWidth']) : 43;			// Largeur de la flèche de navigation sur l'image
		this.imgArrowHeight = (params['imgArrowHeight']) ? parseInt(params['imgArrowHeight']) : 42;			// Hauteur de la flèche de navigation sur l'image
		
		this.imgSuivantThumbs = (params['imgSuivantThumbs']) ? params['imgSuivantThumbs'] : '/ressources/ficheHTML/liste-suivant.png';// Url de l'image de navigation suivante (sous les vignettes)
		this.imgPrecedentThumbs = (params['imgPrecedentThumbs']) ? params['imgPrecedentThumbs'] : '/ressources/ficheHTML/liste-precedent.png';// Url de l'image de navigation précédente (sous les vignettes)
		this.thumbsArrowWidth = (params['thumbsArrowWidth']) ? parseInt(params['thumbsArrowWidth']) : 8;	// Largeur des images de navigation (sous les vignettes)
		this.thumbsArrowHeight = (params['thumbsArrowHeight']) ? parseInt(params['thumbsArrowHeight']) : 8;	// Hauteur des images de navigation (sous les vignettes)
		this.framesTransitionPage = (params['framesTransitionPage']) ? parseInt(params['framesTransitionPage']) : 15;// Nombres de transitions pour changer de page

		this.rootThumbs = (params['rootThumbs']) ? params['rootThumbs'] : 'thumbs';							// Répertoire des vignettes
		this.thumbWidth = (params['thumbWidth']) ? parseInt(params['thumbWidth']) : 68;						// Largeur d'une vignette
		this.thumbHeight = (params['thumbHeight']) ? parseInt(params['thumbHeight']) : 58;					// Hauteur d'une vignette
		
		this.rootImages = (params['rootImages']) ? params['rootImages'] : 'images';							// Répertoire des images
		this.imgWidth = (params['imgWidth']) ? parseInt(params['imgWidth']) : 500;							// Largeur maximale d'une image
		this.imgHeight = (params['imgHeight']) ? parseInt(params['imgHeight']) : 500;						// Hauteur minimale d'une image

		
	/**
	 * Fin de la configuration
	 */
	
	
	
	// Conf à ne pas toucher
	this.isIE = document.all;
	this.isOpera = !!window.opera;
	this.currentOpacity = 1;
	this.currentImg = 0;
	this.transition = false;
	
	this.currentPage = 0;
	this.nbPages = 0;
	this.transitionPage = false;
	
	this.container = document.getElementById(idContainer);
	this.containerName = idContainer;
	this.elts = this.container.getElementsByTagName("li");
	
	

	this.navigationWidth = parseInt(this.thumbWidth * this.cols);

	document.getElementById(this.containerName + 'Navigation').style.width = this.navigationWidth + 'px'; 
	document.getElementById(this.containerName + 'Navigation').style.height = (this.thumbHeight * this.rows + this.thumbsArrowHeight + 10) + 'px'; 
	document.getElementById(this.containerName + 'Navigation').style.overflow = 'hidden';
	document.getElementById(this.containerName + 'NavigationScroll').style.left = '0px';
	
	currentCol = currentRow = origLeft = 0;
	
	preload = new Array();
	
	for (i = 0; i < this.elts.length; i++)
	{
		if (currentRow == this.rows)
		{
			currentRow = 0;
			origLeft += parseInt(this.cols * this.thumbWidth);
			this.nbPages++;
		}
		
		// Preload des images principales
		preload[i] = new Image();
		preload[i].src = this.elts[i].firstChild.src.replace(this.rootThumbs, this.rootImages);
		
		this.elts[i].style.position = 'absolute';
		
		this.elts[i].style.left = parseInt(origLeft + currentCol * this.thumbWidth+1) + 'px';
		this.elts[i].style.top = parseInt(currentRow * this.thumbHeight) + 'px';

		if (++currentCol == this.cols)
		{
			currentCol = 0;
			currentRow++;
		}
		
		this.imgOnmouseout(this.elts[i], i);
		this.elts[i].onmouseover = this.bindArgs(this, this.imgOnmouseover, [this.elts[i], i]);
		this.elts[i].onmouseout = this.bindArgs(this, this.imgOnmouseout, [this.elts[i], i]);
		this.elts[i].onclick = this.bindArgs(this, this.imgClick, [this.elts[i], i]);
	}
	document.getElementById(this.containerName + 'Image').innerHTML = '<img src="' + 
			this.elts[0].firstChild.src.replace(this.rootThumbs, this.rootImages)+
			'" id="'+this.containerName+'img" style="visibility:hidden;" />';
	this.elts[0].style.borderColor = this.borderColorOn;
	this.displayThumbsNavigation();
	setTimeout(this.bindArgs(this, this.imgClick, [this.elts[0], 0]), 500);
};

jsGallery.prototype.bindArgs = function(objet, methode, myArgs) {
  return function() {
    return methode.apply(objet, myArgs);
  }
};


jsGallery.prototype.imgClick = function(obj, num)
{
	if (this.transition === true) return(false);
	old = this.currentImg;
	this.currentImg = num;
	if (this.elts[old])
	{
		this.elts[old].style.borderColor = this.borderColorOff;
		this.imgOnmouseout(this.elts[old], old);
	}
	obj.style.borderColor = this.borderColorOn;
	this.setOpacity(obj, 1);
	src = obj.firstChild.src.replace(this.rootThumbs, this.rootImages);
	this.transition = true;
	this.transitionImage(src);
};


jsGallery.prototype.navClick = function(sens, loadImg)
{
	if (this.transitionPage === true) return(false);
	this.transitionPage = true;
	this.currentTransitionPage = 0;
	if (typeof(loadImg) == 'undefined') loadImg = true;
	this.transitionVignettes(parseInt(this.currentPage + sens), loadImg);
};


jsGallery.prototype.transitionVignettes = function(pageDestination, loadImg)
{
	this.currentTransitionPage++;
	destinationLeft = -1 * this.navigationWidth * pageDestination;
	left = -1 * this.navigationWidth * this.currentPage;
	diff = destinationLeft - left;
	currentLeft = left + diff * Math.sin((this.currentTransitionPage / this.framesTransitionPage) * Math.PI / 2);
	document.getElementById(this.containerName + 'NavigationScroll').style.left = currentLeft + 'px';
	
	if (this.currentTransitionPage < this.framesTransitionPage)
	{
		setTimeout(this.bindArgs(this, this.transitionVignettes, [pageDestination, loadImg]), this.transitionDelay);
	}
	else
	{
		this.transitionPage = false;
		this.currentPage = pageDestination;
		this.displayThumbsNavigation();
		if (loadImg === true)
		{
			currentImg = this.cols * this.rows * this.currentPage;
			this.imgClick(this.elts[currentImg], currentImg);
		}
		
	}

};


jsGallery.prototype.transitionImage = function(src)
{
	if (typeof(src) == 'undefined' && this.currentOpacity < 1)
	{
		this.currentOpacity = Math.min(this.currentOpacity + this.deltaOpacity, 1);
		this.setOpacity(document.getElementById(this.containerName + 'img'), this.currentOpacity);
		setTimeout(this.bindArgs(this, this.transitionImage, []), this.transitionDelay);
	}
	else if (typeof(src) != 'undefined' && this.currentOpacity > 0)
	{
		this.currentOpacity = Math.max(this.currentOpacity - this.deltaOpacity, 0);
		this.setOpacity(document.getElementById(this.containerName + 'img'), this.currentOpacity);
		setTimeout(this.bindArgs(this, this.transitionImage, [src]), this.transitionDelay);
	}
	else if (typeof(src) != 'undefined')
	{
		document.getElementById(this.containerName + 'Image').innerHTML = '<img src="'+src+'" name="galImg" id="'+this.containerName+'img" />';
		this.setOpacity(document.getElementById(this.containerName + 'img'), 0);
		setTimeout(this.bindArgs(this, this.transitionImage, []), this.transitionDelay);
	}
	else
	{
		// Fin de la transition : est-on sur la bonne page ?
		page = Math.floor(this.currentImg / (this.cols * this.rows));
		if (page != this.currentPage)
		{
			this.navClick(page - this.currentPage, false);
		}
		this.transition = false;
		this.displayImageNavigation();
	}
};

jsGallery.prototype.displayImageNavigation = function()
{
	this.displayLeft();
	this.displayRight();
	
	document.getElementById(this.containerName + 'img').onmouseover = this.bindArgs(this, this.setOpacityNavigation, [0.2]);
	document.getElementById(this.containerName + 'img').onmouseout = this.bindArgs(this, this.setOpacityNavigation, [0]);
};


jsGallery.prototype.displayThumbsNavigation = function()
{
	if (document.getElementById(this.containerName + 'TSuivant'))
	{
		document.getElementById(this.containerName + 'Navigation').removeChild(document.getElementById(this.containerName + 'TSuivant'));
	}
	
	if (document.getElementById(this.containerName + 'TPrecedent'))
	{
		document.getElementById(this.containerName + 'Navigation').removeChild(document.getElementById(this.containerName + 'TPrecedent'));
	}

	// AFFICHAGE DES FLECHES DE TRANSITION
	maxHeight = this.rows * this.thumbHeight;
	
	if (this.currentPage > 0) {
		// Image Precedente
		d = document.createElement("IMG");
		d.style.width = this.thumbsArrowWidth + 'px';
		d.style.height = this.thumbsArrowHeight + 'px';
		d.src = this.imgPrecedentThumbs;
		d.id = this.containerName + 'TPrecedent';
		d.style.position = 'absolute';
		d.style.left = '0px';
		d.style.top = parseInt(maxHeight + 10) + 'px';
		d.style.cursor = 'pointer';
		document.getElementById(this.containerName + 'Navigation').appendChild(d);
		this.setOpacity(d, 0.5);
		d.onmouseover = this.bindArgs(this, this.setOpacity, [document.getElementById(this.containerName + 'TPrecedent'), 1]);
		d.onmouseout = this.bindArgs(this, this.setOpacity, [document.getElementById(this.containerName + 'TPrecedent'), 0.5]);
		d.onclick = this.bindArgs(this, this.navClick, [-1]);
	}
	
	if (this.nbPages > 0 && this.nbPages > this.currentPage) {
		// Image Suivante
		d = document.createElement("IMG");
		d.style.width = this.thumbsArrowWidth + 'px';
		d.style.height = this.thumbsArrowHeight + 'px';
		d.src = this.imgSuivantThumbs;
		d.id = this.containerName + 'TSuivant';
		d.style.position = 'absolute';
		d.style.left = parseInt(this.navigationWidth - this.thumbsArrowWidth) + 'px';
		d.style.top = parseInt(maxHeight + 10) + 'px';
		d.style.cursor = 'pointer';
		document.getElementById(this.containerName + 'Navigation').appendChild(d);
		this.setOpacity(d, 0.5);
		d.onmouseover = this.bindArgs(this, this.setOpacity, [document.getElementById(this.containerName + 'TSuivant'), 1]);
		d.onmouseout = this.bindArgs(this, this.setOpacity, [document.getElementById(this.containerName + 'TSuivant'), 0.5]);
		d.onclick = this.bindArgs(this, this.navClick, [1]);
	}
	
};



jsGallery.prototype.setOpacityNavigation = function(opacity)
{
  if (document.getElementById(this.containerName + 'Suivant'))
    this.setOpacity(document.getElementById(this.containerName + 'Suivant'), opacity);
    
  if (document.getElementById(this.containerName + 'Precedent'))
    this.setOpacity(document.getElementById(this.containerName + 'Precedent'), opacity);
};

jsGallery.prototype.displayLeft = function()
{
	if (this.currentImg > 0)
	{
		if (document.galImg)
		{
			w = document.galImg.width;
			h = document.galImg.height;
		}
		else
		{
			w = this.imgWidth;
			h = this.imgHeight;
		}	
		d = document.createElement("DIV");
		d.id = this.containerName + "Precedent";
		d.style.width = this.imgArrowWidth + 'px';
		d.style.height = this.imgArrowHeight + 'px';
		d.style.cursor = 'pointer';
		d.style.position = 'absolute';
		d.style.left = parseInt((this.imgWidth - w) / 2) + 'px';
		d.style.top = parseInt((h - this.imgArrowHeight) / 2) + 'px';
		d.style.backgroundImage = "url('"+this.imgPrecedentImage+"')";
		d.onclick = this.bindArgs(this, this.navImg, [parseInt(this.currentImg) - 1]);
		d.onmouseover = this.bindArgs(this, this.setOpacity, [d, 0.6]);
		d.onmouseout = this.bindArgs(this, this.setOpacity, [d, 0.2]);
		document.getElementById(this.containerName + 'Image').appendChild(d);
		this.setOpacity(d, 0);
	}
};

jsGallery.prototype.displayRight = function()
{
	if (this.currentImg < (this.elts.length - 1))
	{		
		if (document.galImg)
		{
			w = document.galImg.width;
			h = document.galImg.height;
		}
		else
		{
			w = this.imgWidth;
			h = this.imgHeight;
		}
		d = document.createElement("DIV");
		d.id = this.containerName + "Suivant";
		d.style.width = this.imgArrowWidth + 'px';
		d.style.height = this.imgArrowHeight + 'px';
		d.style.cursor = 'pointer';
		d.style.position = 'absolute';
		d.style.left = parseInt(this.imgWidth - this.imgArrowWidth - (this.imgWidth - w) / 2) + 'px';
		d.style.top = parseInt((h - this.imgArrowHeight) / 2) + 'px';
		d.style.backgroundImage = "url('"+this.imgSuivantImage+"')";
		d.onclick = this.bindArgs(this, this.navImg, [parseInt(this.currentImg) + 1]);
		d.onmouseover = this.bindArgs(this, this.setOpacity, [d, 0.6]);
		d.onmouseout = this.bindArgs(this, this.setOpacity, [d, 0.2]);
		document.getElementById(this.containerName + 'Image').appendChild(d);
		this.setOpacity(d, 0);
	}
};

jsGallery.prototype.imgOnmouseout = function(obj, num)
{
	if (num != this.currentImg)
	{
		this.setOpacity(obj, this.thumbOpacityOff);
	}
};

jsGallery.prototype.imgOnmouseover = function(obj, num)
{
  this.setOpacity(obj, 1);
};

jsGallery.prototype.navImg = function(newImg)
{
  this.setOpacityNavigation(0);
	this.imgClick(this.elts[newImg], newImg);
};

jsGallery.prototype.debug = function(msg)
{
	document.getElementById('debug').innerHTML += msg + '<br />';
};

jsGallery.prototype.setOpacity = function(obj, opacity)
{
	if (this.isIE && !this.isOpera)
	{
	    ieOpacity = opacity * 100;
	    obj.style.filter = "alpha(opacity=" + ieOpacity + ")";
	}
	else
	{
		obj.style.opacity = opacity;
	}
};