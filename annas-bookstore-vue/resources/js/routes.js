import Passport from './components/passport/passport.vue';

import AuthorsIndex from './components/authors/index.vue';
import AuthorsCreate from './components/authors/create.vue';
import AuthorsUpdate from './components/authors/update.vue';

import BooksIndex from './components/books/index.vue';
import BooksCreate from './components/books/create.vue';
import BooksUpdate from './components/books/update.vue';

export default [
    {path:'/', component: Passport, name:'passport'},

    {path:'/authors', component: AuthorsIndex, name:'authors.index'},
    {path:'/authors/create', component: AuthorsCreate, name:'authors.create'},
    {path:'/authors/update/:id', component: AuthorsUpdate, name:'authors.update'},

    {path:'/books', component: BooksIndex, name:'books.index'},
    {path:'/books/create', component: BooksCreate, name:'books.create'},
    {path:'/books/update/:id', component: BooksUpdate, name:'books.update'},
];