# movies.py

from flask import Blueprint, request, jsonify, session
import mysql.connector
from mysql.connector import Error
from ..config import DATABASE_CONFIG
from ..app import loggedin, useremail

movies_blueprint = Blueprint('movies', __name__)

def create_connection():
    return mysql.connector.connect(user='root', database='steamdb')

@movies_blueprint.route('/api/movies', methods=['POST'])
def add_movie():
    from ..app import useremail
    data = request.json
    title = data.get('title')
    erscheinungsjahr = data.get('erscheinungsjahr')
    genre_id = data.get('genre')
    dauer = data.get('dauer')
    imdb_link = data.get('imdb_link')
    user_email = useremail
    print(title)
    print(erscheinungsjahr)
    print(genre_id)
    print(dauer)
    print(user_email)
    print(imdb_link)

    if not all([title, erscheinungsjahr, genre_id, dauer, imdb_link, user_email]):
        return jsonify({'error': 'Missing data'}), 400

    try:
        conn = create_connection()
        cursor = conn.cursor()
        

        insert_movie_query = """
        INSERT INTO movies (title, erscheinungsjahr, genre, dauer, link)
        VALUES (%s, %s, %s, %s, %s)
        """
        cursor.execute(insert_movie_query, (title, erscheinungsjahr, genre_id, dauer, imdb_link))
        last_movie_id = cursor.lastrowid
        insert_favorite_query = """
        INSERT INTO user_movies (email, movie) VALUES (%s, %s)
        """
        cursor.execute(insert_favorite_query, (user_email, last_movie_id))

        conn.commit()
        return jsonify({'message': 'Movie added successfully', 'movie_id': last_movie_id}), 201

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()

@movies_blueprint.route('/api/movies/show', methods=['GET'])
def select_movies():
    from ..app import useremail
    user_email = useremail
    print(user_email)
    if not user_email:
        return jsonify({'error': 'User not logged in'}), 401
    
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        
        sql = """
        SELECT movies.title, movies.erscheinungsjahr, movies.link, movies.dauer, genres.genre 
        FROM movies 
        INNER JOIN user_movies ON movies.id = user_movies.movie
        INNER JOIN genres ON movies.genre = genres.id
        WHERE user_movies.email = %s
        """
        cursor.execute(sql, (user_email,))
        movies = cursor.fetchall()
        
        return jsonify(movies), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()