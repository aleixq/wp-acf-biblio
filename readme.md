DESCRIPTION
===========
This plugin is part of a package provided to filter data in a biblioteca in that way: 

-Coming from a page with advanced custom fields (ACF),it has a field with content inside, this content has sub_fields, you can filter all the content by tipo_documento, and after that, with filterset where you can choose fecha and tema. It's static (not exportable) as you need the id of the master post /called biblioteca/, and the exact subfields tipo_documento, tema and fecha, to recreate the query system.
The main function of this plugin is to create query constructor endpoint, and needs a template to make it work as a frontend to this ajax endpoint.

TODO:
=====
As a todo if things go big or if implemented other places:
- Too much query's that returns all nodes (or rows). It returns all items each time you refresh (to build the filters set), and each time it gets items)
- As it works for a few content it's enough with php array diff to filter results, and act as a query. It's not sustainable if goes big, thing in replacing the function filter_biblioitems that basicly do an array_diff_assoc with a real sql query. Query's languages are for this use cases.
- The same is applicable in filter set results. better sql.
- External template for items instead of using the function theme_fields.
- Do ajax alternative (handle php requests???). Create class BiblioBackend with no ajax????
Consulta a traves de items amb array diff millor sql

Debug notes:
===========
- It makes no sense to return each time the raw items, so it's commented, but you can enable if you want. look for all_items in function query_bibliobackend.
