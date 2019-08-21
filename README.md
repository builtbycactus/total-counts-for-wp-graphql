# Total Counts for WP-GraphQL

As it stands WP-GraphQL doesn't implement the `totalCount` field. This is due to a number of reasons, including the performance impact that would come with such a query.

We found that we needed this for a site being developed, so built up this basic plugin.

Whilst this doesn't implement the `totalCount` field as per the [GraphQL docs](https://graphql.org/learn/pagination/#end-of-list-counts-and-connections), it does add a `total` field to the `pageInfo` object that is used for pagination. This seemed like the most sensible place to put it, as 99% of the time, we'll be using this field to pull back the total number of items, alongside our paginated result.



## Retrieving the total count.

To retrieve to total count, it's a simple case of requesting the `total` field with your `pageInfo`. See below:

```js
{
  posts(first:1) {
    edges {
      node {
        id
        title
      }
    }
    pageInfo {
      total
      hasNextPage
      endCursor
    }
  }
}
```

This will then give you a result as such:

```json
{
  "data": {
    "posts": {
      "edges": [
        {
          "node": {
            "id": "cG9zdDozOA==",
            "title": "Post B"
          }
        }
      ],
      "pageInfo": {
        "total": 2,
        "hasNextPage": true,
        "endCursor": "YXJyYXljb25uZWN0aW9uOjM4"
      }
    }
  }
}
```



As you can see, the total number of items is accessible via `data.posts.pageInfo.total`.



## Contributions

Contributions are welcome. This was a very quick build and as such there are probably performance gains to be had.

Feel free to make a PR against this repo!