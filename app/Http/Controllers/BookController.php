<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookAuthor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{

    public function index()
    {
        try {
            $books = Book::with('authors')->get();
            return [
                'status' => true,
                'books' => $books
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'year' => 'required|numeric',
                'authors' => 'array',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'msg' => implode("; ", $validator->messages()->all())
                ];
            }

            $book = Book::create([
                'name' => $request->input('name'),
                'year' => $request->input('year')
            ]);

            $bookAuthors = [];

            foreach ($request->input('authors') as $authorId) {
                $bookAuthors[] = BookAuthor::create([
                    'book_id' => $book->id,
                    'author_id' => $authorId
                ]);
            }

            return [
                'status' => true,
                'book' => $book,
                'bookAuthors' => $bookAuthors
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage() . " on line " . $e->getLine()
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                return [
                    'status' => false,
                    'msg' => 'Book not found'
                ];
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'year' => 'required|numeric',
                'authors' => 'array',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'msg' => implode("; ", $validator->messages()->all())
                ];
            }

            $book->name = $request->input('name');
            $book->year = $request->input('year');

            $book->save();

            $bookAuthors = [];

            $newAuthorsId = $request->input('authors');
            $bookAuthorsIdToDelete = [];
            $oldBookAuthors = BookAuthor::where('book_id', $id)->get()->all();
            foreach ($oldBookAuthors as $bookAuthor) {
                if (($key = array_search($bookAuthor->author_id, $newAuthorsId)) !== false) {
                    unset($newAuthorsId[$key]);
                } else {
                    $bookAuthorsIdToDelete[] = $bookAuthor->id;
                }
            }
            foreach ($bookAuthorsIdToDelete as $bookAuthorId) {
                BookAuthor::find($bookAuthorId)->delete();
            }
            foreach ($newAuthorsId as $authorId) {
                $bookAuthors[] = BookAuthor::create([
                    'book_id' => $book->id,
                    'author_id' => $authorId
                ]);
            }

            return [
                'status' => true,
                'book' => $book,
                'bookAuthors' => $bookAuthors
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage() . " on line " . $e->getLine()
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $res = Book::find($id)->delete();
            return [
                'status' => $res
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage() . " on line " . $e->getLine()
            ];
        }
    }
}
