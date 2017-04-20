var host = location.hostname;
var addUserURL = 'http://' + host + '/adduser';
var editUserURL = 'http://' + host + '/edituser';
var addPostURL = 'http://' + host + '/addpost';
var pagePostURL = 'http://' + host + '/wall/';
var deletePostURL = 'http://' + host + '/deletepost/';
var deleteMediaURL = 'http://' + host + '/delmedia/';
var editPostURL = 'http://' + host + '/editpost/';
var loginURL = 'http://' + host + '/login';
var deleteUserURL = 'http://' + host + '/deluser/';
var deletePollURL = 'http://' + host + '/delpoll/';
var addPoll = 'http://' + host + '/addPoll';


var pageName = document.getElementById('content');
pageName = pageName.className;

$("a[href*=#]").click(function (e) {
    e.preventDefault();
});

/* Ajax loader */

function getXMLHttpRequest() {
    var xhr = null;

    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }

    return xhr;
}

/* Format date */

function IsValidDate(strDate, strDelimiter, iDayPosInArray, iMonthPosInArray, iYearPosInArray) {
    var strDateArr;
    var dtDate;
    var iDay, iMonth, iYear;


    if (null == strDate || typeof strDate != "string")
        return null;

    //defaults
    strDelimiter = strDelimiter || "/";
    iDayPosInArray = undefined == iDayPosInArray ? 0 : iDayPosInArray;
    iMonthPosInArray = undefined == iMonthPosInArray ? 1 : iMonthPosInArray;
    iYearPosInArray = undefined == iYearPosInArray ? 2 : iYearPosInArray;

    strDateArr = strDate.split(strDelimiter);

    iDay = parseInt(strDateArr[iDayPosInArray],10);
    iMonth = parseInt(strDateArr[iMonthPosInArray],10) - 1; // Note: months are 0-based
    iYear = parseInt(strDateArr[iYearPosInArray],10);

    dtDate = new Date(
        iYear,
        iMonth, // Note: months are 0-based
        iDay);

    return (!isNaN(dtDate) && dtDate.getFullYear() == iYear && dtDate.getMonth() == iMonth && dtDate.getDate() == iDay) ? dtDate : null; // Note: months are 0-based
}

/* Message de retour (après validation de formulaire) */

function finalMessage(message) {
    var title = document.getElementById('form-title');
    var errorMessage = document.createElement("div");
    errorMessage.id = 'errorMessage';
    errorMessage.innerHTML = message;
    title.parentNode.insertBefore(errorMessage, title.nextSibling);
    setTimeout(function() {
        $('#errorMessage').fadeOut('slow');
    }, 3000);
}

function insertAfter2(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}


/* Ajout ou modification d'un user */


function userFormSubmit(e) {
    var name = document.getElementById("name").value;
    var password = document.getElementById("password").value;
    if (document.getElementById('email')) {
        var email = document.getElementById("email").value;
    }
    if (document.getElementById('last_classement')) {
        var last_classement = document.getElementById("last_classement").value;
    }
    if (document.getElementById('statut')) {
        var statut = document.getElementById("statut").value;
    }
    var picture = document.querySelector('#picture');

    if (document.getElementById('id')) {
        var id = document.getElementById("id").value;
    }

    if (name == '' || password == '') {
        finalMessage('Veuillez remplir tous les champs obligatoires');
        return false;
    }

    if (password.length < 6) {
        finalMessage('Votre mot de passe doit contenir au moins 6 caractères');
        return false;
    }

    var form = new FormData();
    form.append('name', name);
    form.append('password', password);
    if (email) {
        form.append('email', email);
    }
    if (last_classement) {
        form.append('last_classement', last_classement);
    }
    if (statut) {
        form.append('statut', statut);
    }
    if (document.getElementById("picture").value.length > 0) {
        var filename = document.getElementById("picture").value;
        var format = filename.substr(filename.length - 3);
        if (format == 'png' || format == 'jpg' || format == 'JPG' || format == 'jpeg') {
            form.append('picture', picture.files[0]);
        }
        else {
            finalMessage("Votre photo de profil doit être au format jpg ou png");
            document.getElementById("picture").value = "";
            return false;
        }

        if (document.getElementById("picture").files[0].size > 2000000) {
            finalMessage("Votre photo est trop lourde (2 Mo max)");
            return false;
        }
    }


    if (document.getElementById('profile')) {
        var profile = document.getElementById("profile").value;
        form.append('profile', profile);
    }

    if (typeof id !== 'undefined') {
        form.append('id', id);
    }

    if (typeof profile !== 'undefined') {
        form.append('profile', profile);
    }

    var userform = document.getElementById('userForm');
    var formClass = userform.className;


    if (formClass == 'userAddForm') {
        var URL = addUserURL;
    }
    else {
        var URL = editUserURL;
    }

    document.getElementById('loading_animation').style.display = "inline-block";

    var xhr = getXMLHttpRequest();

    xhr.open("POST", URL, true);
    xhr.setRequestHeader("X-FILENAME", filename);
    xhr.send(form);


    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            document.getElementById('loading_animation').style.display = "none";
            var response = JSON.parse(xhr.responseText);
            var form = document.getElementById('userForm');
            while (form.firstChild) {
                form.removeChild(form.firstChild);
            }
            var returnMessage = document.getElementById('userForm').appendChild(document.createElement('div'));
            returnMessage.style.textAlign = "center";
            returnMessage.innerHTML = response['message'];
            setTimeout(function() {
                location.reload();
            }, 2000);

            return false;
        }
    };
    return false;

}


/* Dropzone (formulaire drag & drop) */

if(window.location.href.indexOf("media") > -1) {
    Dropzone.autoDiscover = false;
    $('#my-awesome-dropzone').attr('class','dropzone');
    var myDropzone = new Dropzone('#my-awesome-dropzone', {
        url:'http://oscars.fr/addmedia',
        clickable:true,
        method:'post',
        maxFiles:1,
        parallelUploads:3,
        maxFilesize:5,
        acceptedFiles: ".png,.jpg,.gif,.jpeg,.JPG",
        addRemoveLinks:true,
        dictRemoveFile:'Remove',
        dictCancelUpload:'Cancel',
        forceFallback:false,
        createImageThumbnails:true,
        maxThumbnailFilesize:1,
        autoProcessQueue:false,
        init:function(){
            var self = this;
            $('#addMedia').on('click', function(e) {
                if (document.getElementById('title').value == '' || document.getElementById('description').value == ''
                    || document.getElementById('date').value == '' || document.getElementById('place').value == '') {
                    document.getElementById('form-title').innerHTML = '<p class="alert-error text-error">Tous les champs de ce formulaire doivent être remplis !';
                    return false;
                }
                var title = $('#title').val();
                var description = $('#description').val();
                var date = $('#date').val();

                var browser = detect.parse(navigator.userAgent);
                browser = browser.browser.family;
                if (browser != 'Chrome') {
                    if (null == IsValidDate(date)) {
                        finalMessage("La date n'est pas au bon format");
                        return false;
                    }
                }

                var place = $('#place').val();
                e.preventDefault();
                e.stopPropagation();
                myDropzone.on("sending", function(file, xhr, formData) {
                    formData.append("title", title);
                    formData.append("description", description);
                    formData.append("date", date);
                    formData.append("place", place);
                });
                myDropzone.processQueue();
            });
            // config
            self.options.addRemoveLinks = true;
            self.options.dictRemoveFile = "Delete";
            //New file added
            self.on("addedfile", function (file) {
                console.log('new file added ', file);
                $('#mediaForm').css('display', 'block');
                $('#addMedia').css('display', 'block');
                $('#cancel_btn').css('display', 'block');
            });
            // Send file starts
            self.on("sending", function (file) {
                console.log('upload started', file);
                $('.meter').show();
            });

            // File upload Progress
            self.on("totaluploadprogress", function (progress) {
                console.log("progress ", progress);
                $('.roller').width(progress + '%');
            });

            self.on("queuecomplete", function (progress) {
                $('.meter').delay(999).slideUp(999);
                setTimeout(function () {
                    location.reload()
                }, 2000);
            });

            // On removing file
            self.on("removedfile", function (file) {
                console.log(file);
            });
        }
    });
}

/* Bouton d'annulation formulaire dropzone */

$('#cancel_btn').click(function (e) {
    $('#mediaForm').toggle();
    if ($(this).text()=='Rétablir') {
        $(this).text('Annuler');
    }
    else {$(this).text('Rétablir');
    }
    e.preventDefault();
});

/* Bouton d'édition formulaire dropzone */


$(".edit_btn").click(function(e) {
    var id = this.id;
    $("#mediaEditForm_"+id).show();
    $("#editMedia_"+id).show();
    $("#cancel_edit_btn_"+id).show();
    e.preventDefault();
});

/* Bouton d'annulation d'édition du formulaire dropzone */

$('.cancel_edit_btn').click(function (e) {
    var id = this.id;
    $('.mediaEditForm').hide();
    e.preventDefault();
});


/* Wall posts (ajout de post) */

if (pageName == 'page_wall') {
    var wallInput = document.getElementById("post_content");
    wallInput.addEventListener("keydown", function (e) {

        if (e.keyCode === 13 && !e.shiftKey) {
            var content = wallInput.value;

            var xhr = getXMLHttpRequest();

            var form = new FormData();

            form.append('content', content);

            xhr.open("POST", addPostURL, true);
            xhr.send(form);

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                    if (xhr.responseText === false) {
                        alert('Problème d\'enregistrement');
                    }

                    else {

                        /* Affichage du nouveau post */

                        var response = JSON.parse(xhr.responseText);

                        var newPost = document.createElement("li");
                        newPost.id = response['post_id'];
                        newPost.className = "wall_rows";
                        var userDiv = newPost.appendChild(document.createElement("div"));
                        userDiv.className = "user-thumb";
                        var userImg = userDiv.appendChild(document.createElement("img"));
                        userImg.src = response['picture'];
                        var subDiv = newPost.appendChild(document.createElement(("div")));
                        subDiv.className = 'post_block';
                        var userInfos = subDiv.appendChild(document.createElement(("span")));
                        userInfos.className = 'user-info';
                        var userPseudo = userInfos.appendChild(document.createElement("span"));
                        userPseudo.className = "user_pseudo";
                        userPseudo.innerHTML = response['name'];
                        var userDate = userInfos.appendChild(document.createElement("span"));
                        userDate.className = "user_date";
                        userDate.innerHTML = " / "+response['date'];
                        var content = subDiv.appendChild(document.createElement("p"));
                        content.className = 'post_content_txt';
                        content.textContent = response['content'];
                        var editInput = subDiv.appendChild(document.createElement("textarea"));
                        editInput.id = 'newContent_'+response['post_id'];
                        editInput.innerHTML = response['content'];
                        editInput.style.display = 'none';
                        var buttonsBlock = document.createElement('div');
                        buttonsBlock.className = 'buttons_block';
                        buttonsBlock.style.display = 'none';
                        var editBtnBlock = buttonsBlock.appendChild(document.createElement("div"));
                        var editBtn = editBtnBlock.appendChild(document.createElement("a"));
                        editBtn.href = '#';
                        editBtn.id = 'edit_btn_'+response['post_id'];
                        editBtn.className = response['post_id'];
                        editBtn.onclick = function() {editPost(this);};
                        var iconEdit = editBtn.appendChild(document.createElement('i'));
                        iconEdit.className = 'icon-edit';
                        var deleteBtnBlock = buttonsBlock.appendChild(document.createElement("div"));
                        var deleteBtn = deleteBtnBlock.appendChild(document.createElement("a"));
                        deleteBtn.href = '#';
                        deleteBtn.id = 'remove_btn_';
                        deleteBtn.className = response['post_id'];
                        deleteBtn.onclick = function () {deletePost(this);};
                        var iconDelete = deleteBtn.appendChild(document.createElement('i'));
                        iconDelete.className = 'icon-remove';
                        iconDelete.id = '/deletepost/:'+response['post_id'];
                        newPost.appendChild(buttonsBlock);
                        var lastPost = document.getElementById('posts_list').firstElementChild;
                        document.getElementById('posts_list').insertBefore(newPost, lastPost);

                        document.getElementById('post_content').value = null;
                    }

                }
                return false;
            }
        }
        return false;
    });
}

/* Affichage d'un bouton "Lire la suite" en cas de post faisant + de 500 caractères */

function truncatePost() {

    var element = document.querySelectorAll("[class='post_content_txt']");
    for (var i=0; i<element.length; i++) {
        var content = element[i].innerHTML;
        if (content.length > 500) {
            var newContent = content.slice(0, 500);
            element[i].style.display = "none";
            var truncateTxt = element[i].parentNode.appendChild(document.createElement("p"));
            truncateTxt.innerHTML = newContent + '...';
            truncateTxt.className = 'post_content_txt_truncate';
            var link = element[i].parentNode.appendChild(document.createElement("a"));
            link.href = "#";
            link.innerHTML = "Lire la suite";
            link.className = "btn_more";
            link.onclick = function(e) {
                e.preventDefault();
                var f = this.parentNode;
                if (f.getElementsByTagName("p")[0].style.display == "none") {
                    f.getElementsByTagName("p")[0].style.display = "block";
                    f.getElementsByTagName("p")[1].style.display = "none";
                    this.innerHTML = "Réduire";

                }
                else {
                    f.getElementsByTagName("p")[0].style.display = "none";
                    f.getElementsByTagName("p")[1].style.display = "block";
                    this.innerHTML = "Lire la suite";
                }

            }
        }
    }
}

/* Affichage des posts correspondants lors de changement de page */

function pagination(elmnt) {
    var numPage = elmnt.className;

    if (elmnt.firstElementChild.className == 'icon-chevron-left') {
        numPage = --numPage;
    }
    else {
        numPage = ++numPage;
    }

    if (numPage != 1) {
        if (document.getElementById('previous_page').parentElement.style.display != 'inline-block') {
            document.getElementById('previous_page').parentElement.style.display = 'inline-block';
        }
    }
    else {
        document.getElementById('previous_page').parentElement.style.display = 'none';
    }

    /* Le formulaire n'est affiché que sur la 1ere page */
    if (numPage!=1) {
        document.getElementById('post_content').style.display = 'none';
    }
    else {
        document.getElementById('post_content').style.display = 'block';
    }
    //
    var xhr = getXMLHttpRequest();
    var form = new FormData();

    form.append('numPage', numPage);

    xhr.open("POST", pagePostURL + numPage, true);
    xhr.send(form);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {

            var response = JSON.parse(xhr.responseText);
            var listPosts = document.getElementById('posts_list');
            while (listPosts.firstChild) {
                listPosts.removeChild(listPosts.firstChild);
            }

            /* Affichage des posts correspondants à la page demandée */

            for (var i = 1; i<response.length; i++) {
                var newPost = document.createElement("li");
                newPost.id = response[i]['id'];
                newPost.className = "wall_rows";
                var userDiv = newPost.appendChild(document.createElement("div"));
                userDiv.className = "user-thumb";
                var userImg = userDiv.appendChild(document.createElement("img"));
                userImg.src = response[i]['picture'];
                var subDiv = newPost.appendChild(document.createElement(("div")));
                subDiv.className = 'post_block';
                var userInfos = subDiv.appendChild(document.createElement(("span")));
                userInfos.className = 'user-info';
                var userPseudo = userInfos.appendChild(document.createElement("span"));
                userPseudo.className = "user_pseudo";
                userPseudo.innerHTML = response[i]['name'];
                var userDate = userInfos.appendChild(document.createElement("span"));
                userDate.className = "user_date";
                userDate.innerHTML = " / "+response[i]['date'];
                var content = subDiv.appendChild(document.createElement("p"));
                content.className = 'post_content_txt';
                content.textContent = response[i]['content'];
                if (response[0] == response[i]['user_id']) {
                    var editInput = subDiv.appendChild(document.createElement("textarea"));
                    editInput.id = 'newContent_'+response[i]['id'];
                    editInput.innerHTML = response[i]['content'];
                    editInput.style.display = 'none';
                    var buttonsBlock = document.createElement('div');
                    buttonsBlock.className = 'buttons_block';
                    buttonsBlock.style.display = 'none';
                    var editBtnBlock = buttonsBlock.appendChild(document.createElement("div"));
                    var editBtn = editBtnBlock.appendChild(document.createElement("a"));
                    editBtn.href = '#';
                    editBtn.id = 'edit_btn_'+response[i]['id'];
                    editBtn.className = response[i]['id'];
                    editBtn.onclick = function() {editPost(this);};
                    var iconEdit = editBtn.appendChild(document.createElement('i'));
                    iconEdit.className = 'icon-edit';
                    var deleteBtnBlock = buttonsBlock.appendChild(document.createElement("div"));
                    var deleteBtn = deleteBtnBlock.appendChild(document.createElement("a"));
                    deleteBtn.href = '#';
                    deleteBtn.id = 'remove_btn';
                    deleteBtn.className = response[i]['id'];
                    deleteBtn.onclick = function () {deletePost(this);};
                    var iconDelete = deleteBtn.appendChild(document.createElement('i'));
                    iconDelete.className = 'icon-remove';
                    iconDelete.id = '/deletepost/:'+response[i]['id'];
                    newPost.appendChild(buttonsBlock);
                }
                var lastPost = document.getElementById('posts_list').firstElementChild;
                document.getElementById('posts_list').insertBefore(newPost, lastPost);
                document.getElementById('previous_page').className = numPage;
                document.getElementById('next_page').className = numPage;
            }
            truncatePost();

            if (document.getElementById('posts_list').getElementsByTagName('li').length != 10) {
                document.getElementById('next_page').parentElement.style.display = "none";
            }
            else {
                document.getElementById('next_page').parentElement.style.display = "inline-block";
            }

            $("a[href*=#]").click(function (e) {
                e.preventDefault();
            });
        }
    };
}

/* Modification d'un post */

function editPost(elmnt) {
    var numPost = elmnt.id;
    numPost = numPost.replace(/\D/g,'');
    var input = document.getElementById('newContent_'+numPost);
    input.style.display = "block";
    $('#'+numPost+' p').hide();
    $('#'+numPost+' .btn_more').hide();

    /* Instructions en cas de click sur le bouton "Annuler" */
    var cancelBtn = input.parentNode.appendChild(document.createElement('button'));
    cancelBtn.innerHTML = "Annuler";
    cancelBtn.className = "btn_cancel";
    cancelBtn.onclick = function () {
        $('#'+numPost+' .post_content_txt').show();
        $('#'+numPost+' .btn_more').show();
        $('#'+numPost+' textarea').hide();
        this.parentElement.removeChild(this);
    };


    input.addEventListener("keydown", function (e) {
        /* Rtour à la ligne si l'utilisateur appuye sur Shift+Entrée */
        if (e.keyCode === 13 && e.shiftKey) {
            this.value += "\n";
        }

        /* Soumission du post si l'utilisateur appuye seulement sur Entrée */
        if (e.keyCode === 13 && !e.shiftKey) {
            var newContent = input.value;
            var originalContent = input.previousElementSibling.innerHTML;
            var post = input.parentNode.parentNode.id;
            if (newContent == originalContent) {
                input.style.display = "none";
                input.previousElementSibling.style.display = "block";
                input.nextElementSibling.style.visibility = "visible";
                var buttonsCancel = document.getElementsByClassName('btn_cancel');
                for (var i = 0; i<buttonsCancel.length; i++) {
                    buttonsCancel[i].style.display = "none";
                }
                return false;
            }
            var xhr = getXMLHttpRequest();
            xhr.open("POST", editPostURL, true);
            var form = new FormData();
            form.append('newContent', newContent);
            form.append('id', numPost);
            xhr.send(form);

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                    if (xhr.responseText) {
                        input.previousElementSibling.innerHTML = xhr.responseText;
                        input.style.display = "none";
                        input.previousElementSibling.style.display = "block";
                        input.nextElementSibling.style.visibility = "visible";
                        var buttonsCancel = document.getElementsByClassName('btn_cancel');
                        for (var i = 0; i<buttonsCancel.length; i++) {
                            buttonsCancel[i].style.display = "none";
                        }

                    }
                    else {
                        alert('Erreur');
                    }
                }
            }
        }
    })

}

/* Authentification */

function login() {
    var pseudo = document.getElementById('pseudo_login').value;
    var password = document.getElementById('password_login').value;

    if (pseudo == '' || password == '') {
        return false;
    }

    var xhr = getXMLHttpRequest();
    var form = new FormData();
    xhr.open('POST', loginURL, true);
    form.append('pseudo', pseudo);
    form.append('password', password);

    xhr.send(form);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            if (xhr.responseText) {
                window.location = "/wall";
            }
            else {
                document.getElementById('login_error').style.display = "block";
            }
        }
    }
}

/* Validation de formulaire par touche "Entrée" */

function submitForm(elmnt) {
    elmnt.addEventListener("keydown", function (e) {
        if(e.keyCode === 13) {
            login();
        }
    })
}

/* Modal Logout */

    $("#btn_logout").click(function() {
        swal({
            title: "Êtes vous sûr de vouloir vous déconnecter ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#27a9e3",
            confirmButtonText: "Oui",
            cancelButtonText: "Annuler",
            closeOnConfirm: true,
            html: false
        }, function(){
            window.location.replace("/logout");
        })
});

/* Modal Suppression élément */


function deletePost(elmnt) {
    swal({
        title: "Êtes vous sûr de vouloir supprimer cet élément ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#27a9e3",
        confirmButtonText: "Oui",
        cancelButtonText: "Annuler",
        closeOnConfirm: true,
        html: false
    }, function(){
        var id = elmnt.className;
        var xhr = getXMLHttpRequest();
        xhr.open("POST", deletePostURL+':'+id, true);
        var form = new FormData();
        form.append('id', id);
        xhr.send(form);

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                if (xhr.responseText) {
                    document.getElementById(id).remove();
                }
                else {
                    alert('Erreur');
                }
            }
        }
    })
}

function deleteMedia(elmnt) {
    var id = elmnt.firstElementChild.id;
    swal({
        title: "Êtes vous sûr de vouloir supprimer cet élément ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#27a9e3",
        confirmButtonText: "Oui",
        cancelButtonText: "Annuler",
        closeOnConfirm: true,
        html: false
    }, function(){
        window.location.replace(deleteMediaURL+':'+id)
    })
}

function deleteUser(elmnt) {
    var user_id = elmnt.firstElementChild.id;
    swal({
        title: "Êtes vous sûr de vouloir supprimer cet élément ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#27a9e3",
        confirmButtonText: "Oui",
        cancelButtonText: "Annuler",
        closeOnConfirm: true,
        html: false
    }, function(){
        window.location.replace(deleteUserURL+':'+user_id);
    })
}

/* Ajustement pour l'animation d'apparition du formulaire d'inscription */

$('#btn_register').click(function() {
    setTimeout( function(){
        $("#loginbox").toggle();
    },800);
});

function insertNewAnswer(counter) {
    var answer_counter = ++counter;
    var answerBlock = document.createElement('div');
    answerBlock.className = 'answer_block';
    var labelAnswer = answerBlock.appendChild(document.createElement('label'));
    labelAnswer.innerText = 'Réponse '+answer_counter;
    var answer = answerBlock.appendChild(document.createElement('textarea'));
    answer.type = 'text';
    answer.className = 'answer';
    answer.id = 'answer_'+answer_counter;
    var addAnswerDiv = answerBlock.appendChild(document.createElement('div'));
    var addAnswer= addAnswerDiv.appendChild(document.createElement('a'));
    addAnswer.className = 'addanswer btn';
    addAnswer.innerText = 'Ajouter une réponse';
    addAnswer.onclick = function() {
        this.parentNode.parentNode.parentNode.appendChild(insertNewAnswer(answer_counter));
        this.style.display = 'none';
    };

    return answerBlock;
}



/* SONDAGES */

/* Animation lors du changement de type de question */
function questionTypeToggle(elmnt) {
    var blockId = elmnt.parentNode.id;
    $('#'+blockId+' .answer_block').toggle();
}

/* Affichage message avant suppression du sondage */
function deletePollForm(elmnt) {
    var el = elmnt.parentNode;
    while ( el.firstChild ) el.removeChild( el.firstChild );
    el.style.display = 'none';
}

/* Ajout d'une nouvelle question */
function insertNewQuestion(iter) {
    var id = iter;
    var mainBlock = document.createElement('div');
    mainBlock.className = 'question_mainblock';
    mainBlock.id = 'question_mainblock_'+id;
    var deleteBtn = mainBlock.appendChild(document.createElement('a'));
    deleteBtn.innerText = 'X';
    deleteBtn.className = 'poll_close_btn btn-large';
    deleteBtn.onclick = function() {deletePollForm(this);};
    var labelQuestion = mainBlock.appendChild(document.createElement('label'));
    labelQuestion.innerText = 'Question '+id;
    var question = mainBlock.appendChild(document.createElement('textarea'));
    question.className = "question";
    question.id = 'question_'+id;
    question.rows = 4;
    var type = mainBlock.appendChild(document.createElement('select'));
    type.className = 'question_type';
    type.onchange = function () {questionTypeToggle(this);};
    var typeOption1 = type.appendChild(document.createElement('option'));
    typeOption1.value = 1;
    typeOption1.innerText = 'QCM';
    var typeOption3 = type.appendChild(document.createElement('option'));
    typeOption3.value = 3;
    typeOption3.innerText = 'Réponse libre';
    mainBlock.appendChild(insertNewAnswer(0));
    if (iter != 1) {
        var linksList = document.getElementsByClassName('addquestion');
        for (var i=0; i < linksList.length; i++) {
            linksList[i].style.display = 'none';
        }
    }
    else {
        var submitBtn = document.getElementById('collapseG2').parentNode.parentNode.appendChild(document.createElement('btn'));
        submitBtn.className = "btn-large btn-success";
        submitBtn.id = "submit_poll";
        submitBtn.innerText = "Enregistrer le sondage";
        submitBtn.onclick = function() {addNewPoll();};
    }
    var addQuestionDiv = document.createElement('div');
    var addQuestion = addQuestionDiv.appendChild(document.createElement('a'));
    addQuestion.className = 'addquestion btn-large';
    addQuestion.innerText = 'Ajouter une question';
    addQuestion.onclick = function () {insertNewQuestion(++id)};
    document.getElementById('collapseG2').appendChild(mainBlock);
    document.getElementById('collapseG2').appendChild(addQuestionDiv);
}

function emptyElement(element) {
    //Removes nulls, zeros (also falses), text version of false, and blank element
    if (element == null || element == 0 || element.toString().toLowerCase() == 'false' || element == '')
        return false;
    else return true;
}

/* Ajout d'un nouveau sondage */
function addNewPoll() {

    if (document.getElementById('new_poll').value) {
        var pollname = document.getElementById('new_poll').value;
    }
    else {
        alert("Veuillez donner un nom à votre sondage");
        return false;
    }
    var polldesc = document.getElementById('new_poll_desc').value;
    var listQuestions = document.getElementsByClassName('question_mainblock');
    var questions = [];
    var questions_type = [];
    var answers = [];

    for (var i =0; i < listQuestions.length; i++) {
        answers[i] = new Array();
        for (var y=0; y < listQuestions[i].childNodes.length; y++) {
            if (listQuestions[i].childNodes[y].className == 'question') {
                questions[i] = listQuestions[i].childNodes[y].value + ';';
            }
            else if (listQuestions[i].childNodes[y].className == 'question_type') {
                questions_type[i] = listQuestions[i].childNodes[y].value;
            }
            else if (listQuestions[i].childNodes[y].className == 'answer_block') {
                answers[i][y] = listQuestions[i].childNodes[y].childNodes[1].value + ';';

            }

        }
    }
    answers = answers.filter(emptyElement);

    var xhr = getXMLHttpRequest();
    var form = new FormData();
    form.append('pollname', pollname);
    form.append('polldesc', polldesc);
    form.append('questions', questions);
    form.append('questions_type', questions_type);
    form.append('answers', answers);
    xhr.open("POST", addPoll, true);
    xhr.send(form);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            if (xhr.responseText == 'OK') {
                location.href = "/polls";
            }
            else {
                alert('Erreur');
            }
        }
    }
}

function deletePoll(elmnt) {
    var poll_id = elmnt.id;
    swal({
        title: "Êtes vous sûr de vouloir supprimer cet élément ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#27a9e3",
        confirmButtonText: "Oui",
        cancelButtonText: "Annuler",
        closeOnConfirm: true,
        html: false
    }, function(){
        window.location.replace(deletePollURL+':'+poll_id);
    })
}










