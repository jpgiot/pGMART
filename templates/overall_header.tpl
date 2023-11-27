{* main header file *}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="shortcut icon" type="image/png" href="images/favicon_pgmart.png" />
  {if $page.title}<title>{$page.title}</title>{/if}

{if $page.includecss}
  <style type="text/css">{include file="css.tpl"}</style>
{/if}
</head>
<body>
{if $page.includebanner}{include file="overall_banner.tpl"}{/if}