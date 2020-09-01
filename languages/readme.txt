#HOW TO TRANSLATE Chat Control Panel

You can translate messages appearing in your chat Control Panel easily with custom build
internationalization script on your chat bot.


For translating chat Control Panel strings you would follow the instructions below:

- Go to your Mr. Assistant Chat Configuration Menu : “Mr. Assistant / Chat Configuration”
- Enable Translation Mode "ON"
- Now go to your Mr. Assistant Chat Control Panel Menu
- Click On String in Gray Border box What you want to translate
- One Pop Up Box will open with previous strings
- Now Translate it and Save it.
- Done!!
- Also You can translate all text from "settings/translation"
- Select One of the page on top to translate.
- Note: Translatable text will Bring up for pages in the time of Browsing on-page. So if you don't show your desire translatable text you need to browse on this portion to extract translatable text.

Note: Translation Mode will overwrite some event in Chat Control Panel.
So After your Translation You must Disable Translation Mode for better Experience.


#HOW TO TRANSLATE Chat Configuration

- Copy/paste that file "wp-content/plugins/mr-assistant/languages/mr-assistant-LOCALE_CODE.po" into
"wp-content/plugins/mr-assistant/languages/" folder
- Find your locale code if you don’t know:
https://wpastra.com/docs/complete-list-wordpress-locale-codes/
- Change the LOCALE_CODE name from file with your desire locale code like that mr-assistant-LOCALE_CODE.po file (i.e. mr-assistant-fr_CA.po is for French (Canada))
- Open this file with Poedit application (www.poedit.net)
- Add your translations to “Translations” part
- Upload your translations (especially mo files) into your “wp-content/languages/” folder

NOTE: Poedit should create .mo files automatically. If not, go to Poedit preferences and
ensure that “Automatically compile .mo file on save” is checked under General settings.
