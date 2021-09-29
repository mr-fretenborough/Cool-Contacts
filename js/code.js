var urlBase = 'https://coolcontacts.xyz/API';
var extension = 'php';

var userId = 0;
var FirstName = "";
var LastName = "";


function doLogin()
{
	userId = 0;
	FirstName = "";
	LastName = "";
	
	var login = document.getElementById("login").value;
	var password = document.getElementById("password").value;
	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

//	var tmp = {Login:login,Password:password};
	var tmp = {Login:login,Password:hash};
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/login.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				var jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.ID;
				console.log(jsonObject);
				console.log(userId);
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				FirstName = jsonObject.FirstName;
				LastName = jsonObject.LastName;
				console.log(FirstName);

				saveCookie();
	
				window.location.href = "home.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}


function registerUser()
{
	var FirstName = document.getElementById("firstName").value;
	var LastName = document.getElementById("lastName").value;
	var Login = document.getElementById("login").value;
	var password = document.getElementById("password").value;
	
	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

//	var tmp = {Login:Login,Password:Password};
	var tmp = {Login:login,Password:hash};
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/login.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				var jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.ID;
		
				if( userId !== 0 )
				{		
					document.getElementById("loginResult").innerHTML = "User already exists";
				}
				else
				{
					var newTmp = {FirstName:FirstName,LastName:LastName,Login:Login,Password:hash}
					var newPayload= JSON.stringify( newTmp );
					var newUrl = urlBase + '/register.' + extension;

					var newXhr = new XMLHttpRequest();
					newXhr.open("POST", newUrl, true);
					newXhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
					try
					{
						newXhr.onreadystatechange = function()
						{
							if (this.readyState == 4 && this.status == 200)
							{
								window.location.href = "index.html";
							}
						};
						newXhr.send(newPayload);
					}
					catch(err)
					{
						document.getElementById("loginResult").innerHTML = err.message;
					}
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}
}

function saveCookie()
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "FirstName=" + FirstName + ",LastName=" + LastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	var data = document.cookie;
	var splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		var thisOne = splits[i].trim();
		var tokens = thisOne.split("=");
		if( tokens[0] == "FirstName" )
		{
			FirstName = tokens[1];
		}
		else if( tokens[0] == "LastName" )
		{
			LastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
}

function doLogout()
{
	userId = 0;
	FirstName = "";
	LastName = "";
	document.cookie = "FirstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	var results = document.getElementById("contact-results");
	var FirstName = document.getElementById("firstNameForm").value;
	var LastName = document.getElementById("lastNameForm").value;
	var PhoneNumber = document.getElementById("phoneForm").value;
	var Email = document.getElementById("emailForm").value;


	readCookie();

	var tmp = {FirstName:FirstName,LastName:LastName,PhoneNumber:PhoneNumber,Email:Email,UserID:userId}
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/create.' + extension;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("firstNameForm").value = "";
				document.getElementById("lastNameForm").value = "";
				document.getElementById("phoneForm").value = "";
				document.getElementById("emailForm").value = "";
				$("#addModal").modal("hide");
				searchContact();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
	
}

function deleteContact(contact)
{
	var FirstName = contact.FirstName; 
	var LastName = contact.LastName; 
	var PhoneNumber = contact.PhoneNumber; 
	var Email = contact.Email; 

	readCookie();

	var tmp = {FirstName:FirstName,LastName:LastName,PhoneNumber:PhoneNumber,Email:Email,UserID:userId}
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/delete.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				var jsonObject = JSON.parse( xhr.responseText );
				var contactResults = document.getElementById("contact-results");
				contactResults.innerHTML = "";
				searchContact();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
}

function searchContact()
{
	var search = document.getElementById("search").value;
	
	readCookie();
	
	var tmp = {search:search,userID:userId};
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/search.' + extension;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				var jsonObject = JSON.parse( xhr.responseText );
				var contactResults = document.getElementById("contact-results");
				contactResults.innerHTML = "";
				for( var i=0; i<jsonObject.results.length; i++ )
				{
					var card = createContact(jsonObject.results[i]);
					contactResults.appendChild(card);	
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
	
}

function createContact(contact)
{
	var card = document.createElement("div");
	var name = document.createElement("h3");
	var phone = document.createElement("p");
	var email = document.createElement("p");
	var edit = document.createElement("button");
	var destroy = document.createElement("button");
	var flex = document.createElement("div");


	flex.classList.add("d-flex", "justify-content-end");
	card.classList.add("card");
	name.innerHTML = contact.FirstName + " " + contact.LastName;
	phone.innerHTML = "Phone: " + contact.PhoneNumber;
	email.innerHTML = "Email: " + contact.Email;
	edit.innerHTML = "Edit";
	destroy.innerHTML = "Delete";

	edit.classList.add("btn", "btn-warning", "edit-btn", "me-1");
	destroy.classList.add("btn", "btn-danger");

	edit.addEventListener("click", function(){editModal(contact)});
	destroy.addEventListener("click", function(){deleteContact(contact)});

	card.appendChild(name);
	card.appendChild(phone);
	card.appendChild(email);
	card.appendChild(flex);
	flex.appendChild(edit);
	flex.appendChild(destroy);

	return card;
}

function editContact(contact)
{
	var results = document.getElementById("contact-results");
	var FirstName = document.getElementById("firstNameForm").value;
	var LastName = document.getElementById("lastNameForm").value;
	var PhoneNumber = document.getElementById("phoneForm").value;
	var Email = document.getElementById("emailForm").value;
	var contactID = contact.contactID;

	readCookie();

	var tmp = {FirstName:FirstName,LastName:LastName,PhoneNumber:PhoneNumber,Email:Email,ID:contactID}
	var jsonPayload = JSON.stringify( tmp );

	var url = urlBase + '/update.' + extension;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("firstNameForm").value = "";
				document.getElementById("lastNameForm").value = "";
				document.getElementById("phoneForm").value = "";
				document.getElementById("emailForm").value = "";
				$("#addModal").modal("hide");
				searchContact();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
}

function addModal()
{
	var modal = $("#addModal");
	var save = document.getElementById("save");
	modal.find(".modal-title").text("Add a contact");
	document.getElementById("firstNameForm").value = "";
	document.getElementById("lastNameForm").value = "";
	document.getElementById("emailForm").value = "";
	document.getElementById("phoneForm").value = "" ;
	modal.modal("toggle");

	save.replaceWith(save.cloneNode(true));
	save = document.getElementById("save");
	save.addEventListener("click", addContact);
}

function editModal(contact)
{
	var modal = $("#addModal");
	var save = document.getElementById("save");
	modal.find(".modal-title").text("Edit contact");
	document.getElementById("firstNameForm").value = contact.FirstName;
	document.getElementById("lastNameForm").value = contact.LastName;
	document.getElementById("emailForm").value = contact.Email;
	document.getElementById("phoneForm").value = contact.PhoneNumber;
	modal.modal("toggle");
	
	save.replaceWith(save.cloneNode(true));
	save = document.getElementById("save");
	save.addEventListener("click", function(){editContact(contact)});
}

if (window.location.pathname == "/home.html")
{
	searchContact();
}
