import JsonApi from 'devour-client';

const jsonApi = new JsonApi({
   apiUrl:'http://annas-bookstore.test/api/v1',
});

const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjMxNGM1YmY4MzVkNmVlMzg3Zjc1MDRhYzA2OTUwMmMwZDcwOWU5YzRiNTMzNDBiNDkzYzFmOTIzNDcyN2Q4MGQyOTU0Yzk0MzhjMTBhZTYxIn0.eyJhdWQiOiIxIiwianRpIjoiMzE0YzViZjgzNWQ2ZWUzODdmNzUwNGFjMDY5NTAyYzBkNzA5ZTljNGI1MzM0MGI0OTNjMWY5MjM0NzI3ZDgwZDI5NTRjOTQzOGMxMGFlNjEiLCJpYXQiOjE1NTg0NDA3NzAsIm5iZiI6MTU1ODQ0MDc3MCwiZXhwIjoxNTkwMDYzMTcwLCJzdWIiOiI2MThjYmU3MC1hNmNjLTQ3OTEtYjQ3NS1kYmZkNTFhYWRmNzMiLCJzY29wZXMiOltdfQ.CHd6HsRS7tJFkVB2dq08JDvlulH9QtFqF9UgtNYj9TmhyUlYNyIj8vTyI-z7PDOd7_931Ln3hnKKfNE8Cotp41v9HfenWU5zDyHiBuUvHZq5SK7UpCJ3xW29c5webDAxWlB4UXzZ_W5RMVcD6jsBdmxX331zA6jRWkuCJX3HVXHaLrJRIdh6hu5ZSeRfm1DJe6L-qTAsTW6g5n_6Kak8suZmQN8x3_U_hgeWnnvW6g_UzQBjb4X0cFbWCpcqMKpmnK2KXf7W3epb8gwpSp_dOkbfbvNiRfBom68XhmwZHw4vqFULjPhyc7raTZs-cj_snUmTgTG5V6tEpuuyehiYSzaoksAmwUYYlxJbDWMHVejPoyLAowCOsn9O-q3GqTT52HDSyG-U2dEFxq-wbJR661Mp0l7p15UpxX8v2SYsxaODnSz26IYa4tEauVX-ZmtvdEvTY9O98XnTvWmqu38GNY0aC-XnESFpOL1dQdHzZSHpt19btEzKmX_muhAFPiYceyzfqLdJcVwAdHKCFiVJ-n1Ngm3iQfBpwW35lbOeptkcWtydxNxqXkBogFxH0NAUXnAfu_gsX_64DKTv9Dv-rNpdeYWEnHhmUEimem1nwF8fNWhxCI7PtHaWP-qlbz1tg1xiNmWhsvcna6R-6InoD-yDgqfaDRIbVkPETmAt3ek';
jsonApi.headers['Authorization'] = `Bearer ${token}`;

jsonApi.define('author', {
    name:'',
    books:{
        jsonApi: 'hasMany',
        type: 'books',
    },
    created_at: '',
    updated_at: '',
});

// Request all authors
jsonApi.findAll('author').then(author => {
    console.table(author.data);
});

// Request a single author
jsonApi.find('author', 1).then(author => {
    console.table(author.data);
});

// Model definition for a book
jsonApi.define('book', {
    title:'',
    description: '',
    publication_year: '',
    authors:{
        jsonApi: 'hasMany',
        type: 'authors',
    },
    created_at: '',
    updated_at: '',
});

// Find all authors and include books
jsonApi.findAll('author', {include: 'books'}).then(author => {
    console.table(author.data);
});


