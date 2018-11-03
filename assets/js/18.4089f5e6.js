(window.webpackJsonp=window.webpackJsonp||[]).push([[18],{146:function(t,a,s){"use strict";s.r(a);var n=s(0),e=Object(n.a)({},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"content"},[s("h1",[t._v("User Query")]),s("div",{staticClass:"tip custom-block"},[s("p",{staticClass:"custom-block-title"},[t._v("Notice")]),s("p",[t._v("We have attached an extra "),s("code",[t._v("organization")]),t._v(" filter criteria to the native "),s("a",{attrs:{href:"https://docs.craftcms.com/v3/element-query-params/user-query-params.html",title:"User Query",target:"_blank",rel:"noopener noreferrer"}},[t._v("User Query"),s("OutboundLink")],1),t._v(".  You can utilize this param on any "),s("a",{attrs:{href:"https://docs.craftcms.com/v3/element-query-params/user-query-params.html",title:"User Query",target:"_blank",rel:"noopener noreferrer"}},[t._v("User Query"),s("OutboundLink")],1)])]),t._m(0),s("p",[t._v("All of the standard "),s("a",{attrs:{href:"https://docs.craftcms.com/v3/element-query-params/user-query-params.html",title:"User Query",target:"_blank",rel:"noopener noreferrer"}},[t._v("User Query"),s("OutboundLink")],1),t._v(" params are available plus the following:")]),s("div",{pre:!0},[s("div",{attrs:{class:"table"}},[s("table",[t._m(1),s("tbody",[s("tr",[t._m(2),s("td",[s("a",{attrs:{href:"http://www.php.net/language.types.string",target:"_blank",rel:"noopener noreferrer"}},[t._v("string"),s("OutboundLink")],1),t._v(", "),s("a",{attrs:{href:"http://www.php.net/language.types.string",target:"_blank",rel:"noopener noreferrer"}},[t._v("string[]"),s("OutboundLink")],1),t._v(", "),s("a",{attrs:{href:"http://www.php.net/language.types.integer",target:"_blank",rel:"noopener noreferrer"}},[t._v("integer"),s("OutboundLink")],1),t._v(", "),s("a",{attrs:{href:"http://www.php.net/language.types.integer",target:"_blank",rel:"noopener noreferrer"}},[t._v("integer[]"),s("OutboundLink")],1),t._v(", "),s("router-link",{attrs:{to:"./../objects/organization.html"}},[t._v("Organization")]),t._v(", "),s("router-link",{attrs:{to:"./../objects/organization.html"}},[t._v("Organization[]")]),t._v(", "),s("a",{attrs:{href:"http://www.php.net/language.types.null",target:"_blank",rel:"noopener noreferrer"}},[t._v("null"),s("OutboundLink")],1)],1),s("td",[t._v("The organization criteria that the resulting users must have")])])])])])]),t._m(3),t._m(4),s("p",[t._v("All of the params above can be accessed and chained together.  The methods are named the same as the property.")]),s("p",[t._v("Here is an example:")]),s("code-toggle",{attrs:{languages:["twig","php"]}},[s("template",{slot:"twig"},[s("div",{staticClass:"language-twig extra-class"},[s("pre",{pre:!0,attrs:{class:"language-twig"}},[s("code",[s("span",{attrs:{class:"token tag"}},[s("span",{attrs:{class:"token ld"}},[s("span",{attrs:{class:"token punctuation"}},[t._v("{%")]),t._v(" "),s("span",{attrs:{class:"token keyword"}},[t._v("set")])]),t._v(" "),s("span",{attrs:{class:"token property"}},[t._v("query")]),t._v(" "),s("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),s("span",{attrs:{class:"token property"}},[t._v("craft")]),s("span",{attrs:{class:"token punctuation"}},[t._v(".")]),s("span",{attrs:{class:"token property"}},[t._v("organizations")]),s("span",{attrs:{class:"token punctuation"}},[t._v(".")]),s("span",{attrs:{class:"token property"}},[t._v("users")]),s("span",{attrs:{class:"token punctuation"}},[t._v(".")]),s("span",{attrs:{class:"token property"}},[t._v("getQuery")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),s("span",{attrs:{class:"token rd"}},[s("span",{attrs:{class:"token punctuation"}},[t._v("%}")])])]),t._v("\n"),s("span",{attrs:{class:"token tag"}},[s("span",{attrs:{class:"token ld"}},[s("span",{attrs:{class:"token punctuation"}},[t._v("{%")]),t._v(" "),s("span",{attrs:{class:"token keyword"}},[t._v("do")])]),t._v(" "),s("span",{attrs:{class:"token property"}},[t._v("query")]),s("span",{attrs:{class:"token punctuation"}},[t._v(".")]),s("span",{attrs:{class:"token property"}},[t._v("organization")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token number"}},[t._v("1")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{attrs:{class:"token punctuation"}},[t._v(".")]),s("span",{attrs:{class:"token property"}},[t._v("firstName")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token string"}},[s("span",{attrs:{class:"token punctuation"}},[t._v("'")]),t._v("Foo"),s("span",{attrs:{class:"token punctuation"}},[t._v("'")])]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),s("span",{attrs:{class:"token rd"}},[s("span",{attrs:{class:"token punctuation"}},[t._v("%}")])])]),t._v("\n")])])])]),s("template",{slot:"php"},[s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[s("span",{attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),s("span",{attrs:{class:"token package"}},[t._v("flipbox"),s("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("organizations"),s("span",{attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Organizations")]),s("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),s("span",{attrs:{class:"token variable"}},[t._v("$query")]),t._v(" "),s("span",{attrs:{class:"token operator"}},[t._v("=")]),t._v(" Organizations"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{attrs:{class:"token function"}},[t._v("getInstance")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{attrs:{class:"token operator"}},[t._v("-")]),s("span",{attrs:{class:"token operator"}},[t._v(">")]),s("span",{attrs:{class:"token function"}},[t._v("getUsers")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{attrs:{class:"token operator"}},[t._v("-")]),s("span",{attrs:{class:"token operator"}},[t._v(">")]),s("span",{attrs:{class:"token function"}},[t._v("getQuery")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{attrs:{class:"token operator"}},[t._v("-")]),s("span",{attrs:{class:"token operator"}},[t._v(">")]),s("span",{attrs:{class:"token function"}},[t._v("organization")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token number"}},[t._v("1")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{attrs:{class:"token operator"}},[t._v("-")]),s("span",{attrs:{class:"token operator"}},[t._v(">")]),s("span",{attrs:{class:"token function"}},[t._v("firstName")]),s("span",{attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{attrs:{class:"token single-quoted-string string"}},[t._v("'Foo'")]),s("span",{attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])])])],2)],1)},[function(){var t=this.$createElement,a=this._self._c||t;return a("h2",{attrs:{id:"params"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#params","aria-hidden":"true"}},[this._v("#")]),this._v(" Params")])},function(){var t=this.$createElement,a=this._self._c||t;return a("thead",[a("tr",[a("th",[this._v("Property")]),a("th",[this._v("Type")]),a("th",[this._v("Description")])])])},function(){var t=this.$createElement,a=this._self._c||t;return a("td",[a("code",[this._v("organization")])])},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"tip custom-block"},[s("p",{staticClass:"custom-block-title"},[t._v("Note")]),s("p",[t._v("The organization criteria (above) can optionally accept three individual criteria:")]),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[s("span",{attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    id"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token number"}},[t._v("1")]),s("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),s("span",{attrs:{class:"token comment"}},[t._v("// The organization identifier(s)")]),t._v("\n    organizationType"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token punctuation"}},[t._v("[")]),s("span",{attrs:{class:"token number"}},[t._v("1")]),s("span",{attrs:{class:"token punctuation"}},[t._v(",")]),s("span",{attrs:{class:"token number"}},[t._v("2")]),s("span",{attrs:{class:"token punctuation"}},[t._v("]")]),s("span",{attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),s("span",{attrs:{class:"token comment"}},[t._v("// The organization type identifier(s)")]),t._v("\n    userType"),s("span",{attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),s("span",{attrs:{class:"token punctuation"}},[t._v("[")]),s("span",{attrs:{class:"token number"}},[t._v("1")]),s("span",{attrs:{class:"token punctuation"}},[t._v(",")]),s("span",{attrs:{class:"token number"}},[t._v("2")]),s("span",{attrs:{class:"token punctuation"}},[t._v("]")]),t._v(" "),s("span",{attrs:{class:"token comment"}},[t._v("// The user type identifier(s)")]),t._v("\n"),s("span",{attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])])},function(){var t=this.$createElement,a=this._self._c||t;return a("h2",{attrs:{id:"chain-setting"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#chain-setting","aria-hidden":"true"}},[this._v("#")]),this._v(" Chain Setting")])}],!1,null,null,null);e.options.__file="user.md";a.default=e.exports}}]);