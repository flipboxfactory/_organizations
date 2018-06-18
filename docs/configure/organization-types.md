# Organization Types

[Organization Types] are classifications that you may assign to an [Organization].  [Organization Types] also have their own (optional) field layout
that can be configured.   

An [Organization] can be assigned to none, one or many [Organization Types].  In addition, the associated [Organization Types] can be sorted to establish a priority.

Common usages of [Organization Types] are:
* Industries
* Association Tiers
* Membership Tiers

### Examples
Spark Technologies is a fortune 500 company; They sponsor (<- an [Organization Type]) an annual conference in exchange for premier advertisement placement and their logo featured on various pages throughout the site.
Since this sponsorship requires a company logo, a special field layout is created to reflects the requirements.  Managing the Organization is intuitive because only the applicable fields are available for content publishing.

::: tip Notice
One may opt to classify [Organizations] using a [Category] or other relation which is acceptable.  The suggested use of [Organization Types] occurs when tailored fields or sortable classifications are needed.
:::

[Category]: https://docs.craftcms.com/api/v3/craft-elements-category.html "Category"

[Organizations]: ../objects/organization.md
[Organization]: ../objects/organization.md
[Organization Types]: ../objects/organization-type.md
[Organization Type]: ../objects/organization-type.md
