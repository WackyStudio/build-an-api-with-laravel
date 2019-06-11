jsonApi.define('author', {
    name: '',
    created_at: '',
    updated_at: '',
    books: {
        jsonApi: 'hasMany',
        type: 'books',
    }
});

jsonApi.define('book', {
    title: '',
    description: '',
    publication_year: '',
    created_at: '',
    updated_at: '',
    authors: {
        jsonApi: 'hasMany',
        type: 'authors',
    },
    comments: {
        jsonApi: 'hasMany',
        type: 'comments',
    }
});