# Nice Backend Previews for Content Elements in TYPO3

## About this extension

This extension adds a hook for rendering content element previews for TYPO3's backend view in the page module,
adding the ability to use Fluid Partials and Layouts to enable consistent preview markup.

## Requirements

* TYPO3 v10.4, v11.5, v12.4, v13.4 or v14
* PHP 7.4 or higher

## Installation

Use composer to add this content element to your project

`composer require b13/backendpreviews`

and install the extension using the Extension Manager in your TYPO3 backend.

## Add configuration

Add this to your PageTsConfig to include the default Fluid Templates provided with this extension:

```
@import 'EXT:backendpreviews/Configuration/PageTs/PageTs.tsconfig'
```

On TYPO3 v13 and v14 you can alternatively include the shipped site set `b13/backendpreviews`
as a dependency of your own site set (`Configuration/Sets/<YourSet>/settings.yaml`) instead of
importing the PageTsConfig manually:

```yaml
dependencies:
  - b13/backendpreviews
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

For plugins a template name for a specific plugin can be specified like this:

```
mod.web_layout.tt_content.preview.template.list.mylist_type = Listtypetemplate
```

All flexform data of the plugin are available in `{pi_flexform_transformed}` to create meaningful previews:

```
<b>Page:</b> {pi_flexform_transformed.settings.page}
```

## Use custom backend previews for default CTypes

Default CTypes for `fluid_styled_content` define dedicated `previewRenderer` classes. If you want to use `EXT:backendpreviews` instead,
remove the configuration for each of these CTypes in your extension's `ext_localconf.php`:

```
unset($GLOBALS['TCA']['tt_content']['types']['textpic']['previewRenderer']);
```

## ViewHelpers

To make it easier to build your own preview templates, the extension ships a few ViewHelpers in the
namespace `B13\Backendpreviews\ViewHelpers`. Register them in your template like this:

```html
<html
	xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
	xmlns:b13="http://typo3.org/ns/B13/Backendpreviews/ViewHelpers"
	data-namespace-typo3-fluid="true"
>
```

* `b13:renderBodytext` – prepares a `bodytext` value for a preview: strips tags (keeping `ol, ul, li`
  by default), crops to a number of characters (`crop`, default `1500`, `0` disables cropping) and
  converts newlines to `<br>`.

  ```html
  {text -> b13:renderBodytext(crop: 200) -> f:format.raw()}
  ```

* `b13:getDatabaseRecord` – fetches database record(s) by uid (or a comma-separated `uidList`) from a
  table (`table`, default `tt_content`) so their fields can be used inside the preview.

* `b13:explodeList` – splits a list value into an array you can iterate over with `f:for`, either by a
  character (`splitChar`, default `,`) or by newlines (`splitNL`).

## License

As TYPO3 Core, _backendpreviews_ is licensed under GPL2 or later. See the LICENSE file for more details.

## Background, Authors & Further Maintenance

`EXT:backendpreviews` was initially created by David Steeb in 2021 for [b13, Stuttgart](https://b13.com). We use this as
a basis to add consistent previews for our custom content element types.

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us
deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term
performance, reliability, and results in all our code.
