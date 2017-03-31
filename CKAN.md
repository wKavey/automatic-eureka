# data.gov CKAN API #

Base URL is `http://catalog.data.gov`. All requests are GETs with an endpoint
like `api/3/action/actionname` where `actionname` can be

* `help_show`: get usage information for an action
* `package_search`: query all data sets
* `group_list`: get list of groups (categories) that data sets can belong to
* `tag_list`: get list of tags that data sets can have

Every action returns a JSON object with keys

* `success`: boolean
* `help`: full URL to get the help for the action (via `help_show`)
* `result`: the action-specific return value

## package_search ##

Inside `result` is `results` containing the actual array of data sets. Each data
set has an array of objects under `resources` which contain metadata about the
actual data making up the data set. Each resource can have several relevant
keys:

* `no_real_name`: this indicates that the data are unnamed. so far I've only
  seen datasets where either everything is named or nothing is.
* `format`: the data format. can be a string like "Zipped CSV" or a MIME type
  like "application/x-ns-proxy-autoconfig" or an empty string.
* `name`: (if `no_real_name` isn't present) the name of the data.

Here are the help docs for `package_search`:

Searches for packages satisfying a given search criteria.

This action accepts solr search query parameters (details below), and
returns a dictionary of results, including dictized datasets that match
the search criteria, a search count and also facet information.

### Solr Parameters: ###

For more in depth treatment of each paramter, please read the [Solr
Documentation](http://wiki.apache.org/solr/CommonQueryParameters)

This action accepts a *subset* of solr's search query parameters:

* :param q: the solr query.  Optional.  Default: `"*:*"`
* :type q: string
* :param fq: any filter queries to apply.  Note: `+site_id:{ckan_site_id}`
    is added to this string prior to the query being executed.
* :type fq: string
* :param sort: sorting of the search results.  Optional.  Default:
    `'relevance asc, metadata_modified desc'`.  As per the solr
    documentation, this is a comma-separated string of field names and
    sort-orderings.
* :type sort: string
* :param rows: the number of matching rows to return.
* :type rows: int
* :param start: the offset in the complete result for where the set of
    returned datasets should begin.
* :type start: int
* :param facet: whether to enable faceted results.  Default: `True`.
* :type facet: string
* :param facet.mincount: the minimum counts for facet fields should be
    included in the results.
* :type facet.mincount: int
* :param facet.limit: the maximum number of values the facet fields return.
    A negative value means unlimited. This can be set instance-wide with
    the :ref:`search.facets.limit` config option. Default is 50.
* :type facet.limit: int
* :param facet.field: the fields to facet upon.  Default empty.  If empty,
    then the returned facet information is empty.
* :type facet.field: list of strings

The following advanced Solr parameters are supported as well. Note that
some of these are only available on particular Solr versions. See Solr's
[dismax][dismax] and [edismax][edismax] documentation for further details on them:

`qf`, `wt`, `bf`, `boost`, `tie`, `defType`, `mm`

[dismax]: http://wiki.apache.org/solr/DisMaxQParserPlugin
[edismax]: http://wiki.apache.org/solr/ExtendedDisMax

### Results: ###

The result of this action is a dict with the following keys:

* :rtype: A dictionary with the following keys
* :param count: the number of results found.  Note, this is the total number
    of results found, not the total number of results returned (which is
    affected by limit and row parameters used in the input).
* :type count: int
* :param results: ordered list of datasets matching the query, where the
    ordering defined by the sort parameter used in the query.
* :type results: list of dictized datasets.
* :param facets: DEPRECATED.  Aggregated information about facet counts.
* :type facets: DEPRECATED dict
* :param `search_facets`: aggregated information about facet counts.  The outer
    dict is keyed by the facet field name (as used in the search query).
    Each entry of the outer dict is itself a dict, with a "title" key, and
    an "items" key.  The "items" key's value is a list of dicts, each with
    "count", "display_name" and "name" entries.  The display_name is a
    form of the name that can be used in titles.
* :type `search_facets`: nested dict of dicts.
* :param `use_default_schema`: use default package schema instead of
    a custom schema defined with an IDatasetForm plugin (default: False)
* :type `use_default_schema`: bool

An example result:

    {'count': 2,
     'results': [ { <snip> }, { <snip> }],
     'search_facets': {u'tags': {'items': [{'count': 1,
                                            'display_name': u'tolstoy',
                                            'name': u'tolstoy'},
                                           {'count': 2,
                                            'display_name': u'russian',
                                            'name': u'russian'}
                                          ]
                                }
                      }
    }

### Limitations: ###

The full solr query language is not exposed. The parameter that controls which
fields are returned in the solr query cannot be changed.  CKAN always returns
the matched datasets as dictionary objects.
