<?php
$string = '<a title="title" href="http://example.com">Text</a>';

$string = preg_replace("/<a\s(.+?)>(.+?)<\/a>/is", "<span>$2</span>", $string);

echo($string);
?>

<!--===============================micro data for breadcrum open=============--->

<!--
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "item": {
      "@id": "https://example.com/arts",
      "name": "Arts"
    }
  },{
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "https://example.com/arts/books",
      "name": "Books"
    }
  },{
    "@type": "ListItem",
    "position": 3,
    "item": {
      "@id": "https://example.com/arts/books/poetry",
      "name": "Poetry"
    }
  }]
}
</script>
-->
<!--===============================micro data for breadcrum =============--->
