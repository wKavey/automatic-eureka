# Dataset Search Engine - CSE 445 Project 1
The goal of our project is to provide a more in-depth and user-friendly search interface for the data available from data.gov. The data.gov site provides two methods of search: a programmer-oriented API (CKAN) for searching the metadata fields included with each data set, and a non-programmer-oriented text search that simply searches the titles and descriptions of each data set.
We want to build a tiered index based on different components of the data sets: titles and descriptions, but also table fields, data types, and other metadata not available on CKAN. This kind of indexing should make it possible to build a more informative set of results than data.gov’s simple search while not overwhelming the user with information like CKAN’s results.
We can roughly break down the project into separate stages:

1. Exploring CKAN to find which data are useful
2. Building parsers to actually inspect the contents of each data set in order to find additional indexable information
3. Building the tiered index
4. Designing the relevance measure (weighting certain fields, cosine, …)
5. Designing the user interface

We will be using Python 3 for all components. As of now we have not found any open source search engines that clearly meet our design goals, so we will potentially be implementing most of the core functionality from scratch (though libraries such as nltk, Whoosh, or scipy could prove useful).
