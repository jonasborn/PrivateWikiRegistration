# About
This is a custom plugin for the muc ccc group called Elektronikfreunde.
Because of some copyright reasons, it was needed to create a customized invite based
registration system.

#Usage
The plugin is made out of three main sites:

## Special:Register
This public page is used to let users register them self. There is only an input for an username.
The template of this page is set in the language files and currently Template:Register | Vorlage:Registrieren (de).
After the registration is done, another page with a template Template:Summary | Vorlage:Zusammenfassung (de) is shown.

## Special:Confirm
A internal page, only visible to certain administrative groups, able to
list all pending registrations.
It shows a list simple list with the username, a link to delete and a link to confirm the request.
When confirmed, a invite link is generated and shown.

## Special:Join
This page is only used together with an invite link. It will force the new user to set
a password before being able to login.
A template called Special:Join | Spezial:Beitreten (de) is used to get the form html structure