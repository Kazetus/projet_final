"use strict";
// déclaration de variables
let contentDisplayer; // afficheur du contenu des utilisateurs sur la page d'accueil.
let btnfiltre; // ciblage des catégorie.
let btnpanel;
let panelDisplayer;
let pass;
let passconfirm;
let validpass;
let btndelete;
let btnmodify;
let research;
//Fonction

// Récupérer les images en BDD dans une catégorie choisie
function loadImageByCategorie(){
   let id = this.dataset.id;
   for(let i=0; i < btnfiltre.length; i++) {
      btnfiltre[i].removeEventListener('click', loadImageByCategorie);
      btnfiltre[i].removeEventListener('click', loadLastImage);
      btnfiltre[i].addEventListener('click', loadImageByCategorie);
   }
   this.removeEventListener('click', loadImageByCategorie);
   this.addEventListener('click', loadLastImage);
   $.get("index.php","action=loadcontent&id="+id,displayImage);
}
// Récupérer les dernières images uploadés par les utilisateurs
function loadLastImage(){
   this.removeEventListener('click', loadLastImage);
   this.addEventListener('click', loadImageByCategorie);
   $.get("index.php","action=load", displayLastImage);
}
// Afficher les dernières images postées si l'utilisateur réappuie sur une catégorie.
function displayLastImage(images) {
   images= JSON.parse(images);
   if(images !== false) {
      contentDisplayer.innerHTML = "<h2>Derniers ajouts :</h2><div id='dynamicdisplay' class='article__display'></div>";
      let dynamicContent = document.getElementById('dynamicdisplay');
      for (let i=0; i< images.length;i++) {
         dynamicContent.innerHTML += `<div class="article__card">
                                          <h3 class="article__title"><a href="index.php?action=image&id=${images[i].ID_image}"><?=${images[i].title}</a></h3>
                                          <p>Posté par <a href="index.php?action=user&id=${images[i].ID_user}">${images[i].pseudo}</a> dans ${images[i].categorieName}</p>
                                          <p class="article__date">${images[i].date}</p>
                                          <a href="index.php?action=image&id=${images[i].ID_image}"><img src="img/${images[i].image}" alt="${images[i].details}"/></a>
                                          <p>${images[i].text}</p>
                                          <p><a href="index.php?action=star&id=${images[i].ID_image}"><i class="fa-solid fa-star"></i></a>${images[i].number}</p>
                                       </div>`;
      }
   }
   else {
   }
}
// Afficher les images récupérés avec les fonctions précédentes
function displayImage(images){
   images = JSON.parse(images);
   contentDisplayer.innerHTML = "<h2>Dernieres images dans "+images[0].categorieName+" :</h2><div id='dynamicdisplay' class='article__display'></div>";
   let dynamicContent = document.getElementById('dynamicdisplay');
   if(images !== false) {
      for (let i=0; i< images.length;i++) {
         dynamicContent.innerHTML += `<div class="article__card">
                                          <h3 class="article__title"><a href="index.php?action=image&id=${images[i].ID_image}"><?=${images[i].title}</a></h3>
                                          <p>Posté par <a href="index.php?action=user&id=${images[i].ID_user}">${images[i].pseudo}</a> dans ${images[i].categorieName}</p>
                                          <p class="article__date">${images[i].date}</p>
                                          <a href="index.php?action=image&id=${images[i].ID_image}"><img src="img/${images[i].image}" alt="${images[i].details}"/></a>
                                          <p>${images[i].text}</p>
                                          <p><a href="index.php?action=star&id=${images[i].ID_image}"><i class="fa-solid fa-star"></i></a>${images[i].number}</p>
                                       </div>`;
      }
   }
   else {
   }
}
// charger le contenu ou les détails du compte de l'utilisateur.
function loadCustomerPanel() {
   let action = "action=" + this.dataset.action;
   if(this.dataset.action === "editaccount") {
      $.get("index.php", action, displayUserForm);
   }
   else {
      $.get("index.php", action, displayUserImage);
   }
}
// afficher un formulaire pour modifier le compte de l'utilisateur ou le contenu posté par celui-ci.
function displayUserForm(content) {
   content = JSON.parse(content);
   panelDisplayer.innerHTML = "";
   panelDisplayer.innerHTML = "<h2> Modifier vos informations </h2>";
   panelDisplayer.innerHTML += `<form action='index.php?action=updateaccount' method='POST' enctype="multipart/form-data"><fieldset class="panel__fieldset" id='forminfo'><legend>Vos informations :`;
   let formDisplayer = document.getElementById("forminfo");
   formDisplayer.innerHTML += `<label class="panel__label" for='pseudo'>Pseudo :</label><input class="panel__input" type='text' id='pseudo' name='pseudo' value='${content.pseudo}'/>`;
   formDisplayer.innerHTML += `<label class="panel__label" for='mail'>Adresse Mail :</label><input class="panel__input" type='email' pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.[a-zA-Z.]{2,15}" id='mail' name='mail' value='${content.mail}'/>`;
   formDisplayer.innerHTML += `<label class="panel__label" for='avatar'>Votre avatar :</label><input class="panel__input" type='file' id='avatar' name='avatar' accept='image/png, image/jpeg'/>`;
   if(content.detailUtilisateur !== null) {
      formDisplayer.innerHTML += `<label class="panel__label" for='detailutilisateur'>A propos de vous : </label><textarea class="panel__input" id='detailutilisateur' name='detailutilisateur'>${content.detailUtilisateur}</textarea>`;
   }
   else {
      formDisplayer.innerHTML += `<label class="panel__label" for='detailutilisateur'>A propos de vous : </label><textarea class="panel__input" id='detailutilisateur' name='detailutilisateur'></textarea>`;
   }
   formDisplayer.innerHTML += "<button type='submit'>Envoyer</button>";
   panelDisplayer.innerHTML += `<form action='index.php?action=newpass' method='POST'><fieldset class="panel__fieldset" id='formpassword'><legend>Modifier votre mot de passe :`;
   let formSecondDisplayer = document.getElementById("formpassword");
   formSecondDisplayer.innerHTML += `<label class="panel__label" for='oldpass'>Ancien mot de passe : </label><input class="panel__input" type='password' id='oldpass' name='oldpass'/>
                                     <label class="panel__label" for='password'>Nouveau mot de passe : </label><input class="panel__input" type='password' id='password' name='password'/>
                                     <label class="panel__label" for='passwordconfirm'>Confirmer le nouveau mot de passe : </label><input class="panel__input" type='password' id='passwordconfirm' name='passwordconfirm'/>
                                     <button type='submit' id='validpassword' class='disabled'>Envoyer</button>`;
   pass= document.getElementById("password");
   passconfirm = document.getElementById("passwordconfirm");
   pass.addEventListener('keyup', checkPasswordEgality);
   passconfirm.addEventListener('keyup', checkPasswordEgality);
}
function displayUserImage(content){
   console.log(content);
   content = JSON.parse(content);
   console.log(content);
   panelDisplayer.innerHTML = "";
   panelDisplayer.innerHTML = "<h2>Mes images téléversées :</h2><div id='article__container' class='article__container'>";
   let articleDisplay = document.getElementById('article__container');
   for(let i=0; i<content.length;i++) {
      articleDisplay.innerHTML += `
                                 <article class="article__card">
                                    <h3 class="article__title"><a href="index.php?action=image&id=${content[i].ID_image}">${content[i].title}</a></h3>
                                    <p class="article__date">Posté le ${content[i].date} dans ${content[i].categorieName}. ${content[i].number} <i class="fa-solid fa-star"></i>.</p>
                                    <img src="img/${content[i].image}" alt="${content[i].details}"/>
                                    <p>${content[i].text}</p>
                                    <button class="panel__button delete" data-action="deleteimage" data-id="${content[i].ID_image}">supprimer</button>
                                    <button class="panel__button modify" data-action="getimage" data-id="${content[i].ID_image}">modifier</button>
                                 </article>
                                 `;
   }
   btndelete = document.getElementsByClassName('delete');
   btnmodify = document.getElementsByClassName('modify');
   createEvent();
}
// Vérification que les champs mot de passe et confirmer mot de passe sont égaux.
function checkPasswordEgality() {
   validpass = document.getElementById("validpassword");
   if(pass.value === passconfirm.value) {
      validpass.classList.remove("disabled");
      validpass.classList.add('enabled');
   }
   else {
      validpass.classList.add("disabled");
      validpass.classList.remove('enabled');
   }
}
// Suppression d'une image utilisateur.
function deleteImage() {
   console.log(this);
   let action = this.dataset.action;
   let id = this.dataset.id;
   if(window.confirm("Voulez-vous vraiment supprimer cette image ?")) {
      $.post('index.php?action='+action, 'id='+id, displayUserImage);
   }
}
// Modification d'un post utilisateur.
function modifyImage() {
   let action = this.dataset.action;
   let id= this.dataset.id;
   $.post("index.php?action="+action, "id="+id, formImage);
}

function formImage(content) {
   content = JSON.parse(content);
   console.log(content);
   content.text = restaureHtml(content.text);
   console.log(content);
   let forminfo = document.getElementById('formPanel');
   let imageinput = document.getElementById('image');
   let imagelabel = document.getElementById('imagelabel');
   let idimage = document.getElementById('hiddenID');
   if(imageinput !== null || imagelabel !== null) {
      forminfo.removeChild(imageinput);
      forminfo.removeChild(imagelabel);
   }
   if (idimage !== null) {
      forminfo.removeChild(idimage);
   }
   forminfo.innerHTML += `<input type='hidden' id='hiddenID' name='ID_image' value='${content.ID_image}'/>`;
   document.getElementById('text').value = content.text;
   document.getElementById('title').value = content.title;
   document.getElementById('details').value = content.details;
   document.getElementById('categorie').value = content.ID_categorie;
   document.getElementById('form').setAttribute('action','index.php?action=modifyimage');
}
// Fonction recherche
function loadImageWithWord(){
   let data = research.value;
   if(data === "") {
      $.get("index.php","action=load", displayLastImage);
   }
   else {
      $.post('index.php?action=search','data='+data, displayResearchImage);
   }
}
// Affichage de la recherche
function displayResearchImage(content) {
      content = JSON.parse(content);
      if(content === "Aucun résultat") {
         contentDisplayer.innerHTML = "<h2>Résultat de votre recherche :</h2><p> Aucun résultat </p>";
      }
      else {
         contentDisplayer.innerHTML = "<h2>Résultat de votre recherche :</h2>";
         for (let i=0; i< content.length;i++) {
            contentDisplayer.innerHTML += `<div>
                                             <h3 class="article__title"><a href="index.php?action=image&id=${content[i].ID_image}">${content[i].title}</a></h3>
                                             <p class="article__date">Posté le ${content[i].date} dans ${content[i].categorieName}. ${content[i].number} Star.</p>
                                             <img src="img/${content[i].image}" alt="${content[i].details}"/>
                                             <p>${content[i].text}</p>
                                          </div>`;
         }
      }
}
// Restauration de certains caractères spéciaux dans un texte utilisateur pour l'édition.
function restaureHtml(text) {
  return text
      .replace(/&amp;/g, "&")
      .replace(/&quot;/g, '"')
      .replace(/&#039;/g, "'");
}
// Génération des divers évènements
function createEvent() {
   if (btnfiltre != null) {
      for(let i=0;i<btnfiltre.length;i++) {
         btnfiltre[i].addEventListener('click', loadImageByCategorie);
      }
   }
   if (btnpanel != null) {
      for(let i=0;i<btnpanel.length;i++) {
         btnpanel[i].addEventListener('click', loadCustomerPanel);
      }
   }
   if(pass != null) {
      pass.addEventListener('keyup', checkPasswordEgality);
      passconfirm.addEventListener('keyup', checkPasswordEgality);
   }
   if (btndelete != null) {
      for(let i=0;i<btndelete.length;i++) {
         btndelete[i].addEventListener('click', deleteImage);
      }
   }
   if (btnmodify !=null) {
      for(let i=0;i<btnmodify.length;i++) {
         btnmodify[i].addEventListener('click', modifyImage);
      }
   }
   if(research != null) {
      research.addEventListener('keyup', loadImageWithWord);
   }
   if(pass != null && passconfirm != null ){
      pass.addEventListener('keyup', checkPasswordEgality);
      passconfirm.addEventListener('keyup', checkPasswordEgality);
   }
}

// gestionnaire d'évènement

document.addEventListener("DOMContentLoaded", function(){
   // attribution des éléments du DOM.
   contentDisplayer = document.getElementById("mainPage");
   btnfiltre = document.getElementsByClassName("filtre__liste__child");
   btnpanel = document.getElementsByClassName("panel__button");
   panelDisplayer = document.getElementById("displayer__panel");
   pass= document.getElementById("password");
   passconfirm = document.getElementById("passwordconfirm");
   btndelete = document.getElementsByClassName('delete');
   btnmodify = document.getElementsByClassName('modify');
   research = document.getElementById('search');
   pass= document.getElementById("password");
   passconfirm = document.getElementById("passwordconfirm");
   //fonction
   createEvent();
});