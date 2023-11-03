# EXT:backendpreviews

## About this extension

This extension adds a hook for rendering content element previews for TYPO3's backend view in the page module, 
adding the ability to use Fluid Partials and Layouts to enable consistent preview markup.

## Installation

Use composer to add this content element to your project

`composer require b13/backendpreviews`

and install the extension using the Extension Manager in your TYPO3 backend.

## Add configuration

Add this to your PageTsConfig to include the default Fluid Templates provided with this extension:

```
@import 'EXT:backendpreviews/Configuration/PageTs/PageTs.tsconfig'
```

You can add your own paths to the setup using PageTsConfig in your own site-extension:

```
mod.web_layout.tt_content.preview.view {
  layoutRootPaths.10 = EXT:site_example/Resources/Private/Contenttypes/Backend/Layouts
  partialRootPaths.10 = EXT:site_example/Resources/Private/Contenttypes/Backend/Partials
  templateRootPaths.10 = EXT:site_example/Resources/Private/Contenttypes/Backend/Templates
}
```

By default, we will try to find a template to render a preview based on the CType of the element, 
meaning for CType `mytype` we will try to find a template named `Mytype.html` in one of the paths defined
in the `templateRootPaths`-Array.

You can set a different templateName explicitly like this:

```
mod.web_layout.tt_content.preview.template.mytype = Myowntemplate
```

## Use custom backend previews for default CTypes

Default CTypes for `fluid_styled_content` define dedicated `previewRenderer` classes. If you want to use `EXT:backendpreviews` instead, 
remove the configuration for each of these CTypes in your extensions's `ext_localconf.php`:

```
unset($GLOBALS['TCA']['tt_content']['types']['textpic']['previewRenderer']);
```

## License

As TYPO3 Core, _backendpreviews_ is licensed under GPL2 or later. See the LICENSE file for more details.

## Background, Authors & Further Maintenance

`EXT:backendpreviews` was initially created by David Steeb in 2021 for [b13, Stuttgart](https://b13.com). We use this as 
a basis to add consistent previews for our custom content element types. 

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us
deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term
performance, reliability, and results in all our code.
