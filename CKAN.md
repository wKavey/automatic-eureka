# data.gov CKAN API #

Base URL is `http://catalog.data.gov`. All requests are GETs with an endpoint
like `api/3/action/actionname` where `actionname` can be

* `help_show`: get usage information for an action
* `package_list`: get list of data sets' metadata
* `package_search`: query all data sets
* `group_list`: get list of groups (categories) that data sets can belong to
* `tag_list`: get list of tags that data sets can have

Every action returns a JSON object with keys

* `success`: boolean
* `help`: full URL to get the help for the action (via `help_show`)
* `result`: the action-specific return value
