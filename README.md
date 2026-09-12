# Nice Backend Previews for Content Elements in TYPO3

## About This Extension

This extension takes over the preview rendering for content elements in TYPO3's page module,
adding the ability to use Fluid templates, layouts, and partials to enable consistent preview markup.

## Requirements

* TYPO3 v13.4 or v14
* PHP 8.2 or higher

Version 2.0 dropped support for TYPO3 v10.4, v11.5, and v12.4 together with the compatibility layer
for those versions. If you are still on one of them, stay on the 1.5 releases.

Release notes live in [CHANGELOG.md](CHANGELOG.md).

## Installation

Use composer to add this extension to your project

`composer require b13/backendpreviews`

and set it up with `vendor/bin/typo3 extension:setup`.

## Add Configuration

Add this to your PageTsConfig to include the default Fluid templates provided with this extension:

```
@import 'EXT:backendpreviews/Configuration/PageTs/PageTs.tsconfig'
```

Alternatively, include the shipped site set `b13/backendpreviews` as a dependency of your own site
set (`Configuration/Sets/<YourSet>/config.yaml`) instead of importing the PageTsConfig manually:

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

## What Your Template Gets

TYPO3 v14 hands the content element to a preview renderer as a record object, while v13 still passes
a plain array (see
[Breaking-92434](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-92434-UseRecordAPIInPageModulePreviewRendering.html),
which is also the migration guide for your own templates). The variables in a preview template
therefore differ between the two versions.

On **v14**, everything from the element itself hangs off a single `{record}`:

```html
<h2>{record.header}</h2>
<p>{record.bodytext}</p>
<f:if condition="{record.image}">Image UID: {record.image.uid}</f:if>
<small>{record.pi_flexform.sheets.s_messages.settings.welcome_header}</small>
```

Relations are already resolved on that object, and flexform values come as a `FlexFormFieldValues`
object grouped by sheet.

On **v13**, the fields of the `tt_content` row are assigned individually, so the same template reads
`{header}`, `{bodytext}`, and `{image}`. This extension adds three things that only exist on that
version, because v14 covers them through the record:

* `{pi_flexform_transformed}` – all flexform data of the plugin as a flat array:

  ```
  <b>Page:</b> {pi_flexform_transformed.settings.page}
  ```

* `{allImages}`, and `{all<Fieldname>}` for every other TCA field of type `file` – the file
  references of that field, which is what the shipped `Images` partial expects.
* `{list_type-label}`, plus a template name per plugin for elements with CType `list`:

  ```
  mod.web_layout.tt_content.preview.template.list.mylist_type = Listtypetemplate
  ```

  There is no v14 counterpart—`list_type` is gone from the core.

On **both** versions this extension assigns:

* `{editLink.url}` and `{editLink.title}` – the edit link for the element, if the user may edit it.
  On v14 there is also `{editLink.contextual}`, which the shipped layouts use to render a
  `typo3-backend-contextual-record-edit-trigger`.
* `{CType-label}` – the resolved label of the element's CType.

If your templates build on the shipped layouts and partials, the version switch is handled there.
A template that reads element fields directly needs both spellings to work on v13 and v14.

## Use Custom Backend Previews for Default CTypes

This extension registers its preview renderer for the whole `tt_content` table. A renderer that is
registered for a single type wins over that, so an element whose type brings its own
`previewRenderer` is not rendered by this extension. The CTypes of `fluid_styled_content` no longer
register one, but other extensions still do—`EXT:form`, for instance, for `form_formframework`.
To use `EXT:backendpreviews` for such a type, remove that configuration in your own extension's
`Configuration/TCA/Overrides/tt_content.php`:

```
unset($GLOBALS['TCA']['tt_content']['types']['form_formframework']['previewRenderer']);
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

## Security

Please report security issues to [security@b13.com](mailto:security@b13.com). See
[SECURITY.md](SECURITY.md) for the reporting process and what to expect.

## License

As TYPO3 Core, _backendpreviews_ is licensed under GPL-2.0-or-later. See the [LICENSE](LICENSE) file
for more details.

## Credits

`EXT:backendpreviews` was created by David Steeb and is maintained by [b13 GmbH](https://b13.com),
Stuttgart, Germany. We use it as a basis to add consistent previews for our custom content element
types.

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you?utm_source=backendpreviews&utm_medium=readme)
that help us deliver value in client projects. As part of our work, we focus on testing and best
practices to ensure long-term performance, reliability, and results in all our code.
