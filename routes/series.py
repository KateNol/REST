# series.py

from flask import Blueprint, request, jsonify, session
import mysql.connector
from mysql.connector import Error
from ..config import DATABASE_CONFIG

series_blueprint = Blueprint('series', __name__)

def create_connection():
    return mysql.connector.connect(user='root', database='steamdb')

@series_blueprint.route('/api/series', methods=['POST'])
def add_series():
    from ..app import useremail
    data = request.json
    title = data.get('title')
    erscheinungsjahr = data.get('erscheinungsjahr')
    genre_id = data.get('genre')
    staffeln = data.get('staffeln')
    imdb_link = data.get('imdb_link')
    user_email = useremail
    print(title)
    print(erscheinungsjahr)
    print(genre_id)
    print(staffeln)
    print(user_email)
    print(imdb_link)

    if not all([title, erscheinungsjahr, genre_id, staffeln, imdb_link, user_email]):
        return jsonify({'error': 'Missing data'}), 400

    try:
        conn = create_connection()
        cursor = conn.cursor()
        

        insert_series_query = """
        INSERT INTO serien (title, erscheinungsjahr, genre, staffeln, link)
        VALUES (%s, %s, %s, %s, %s)
        """
        cursor.execute(insert_series_query, (title, erscheinungsjahr, genre_id, staffeln, imdb_link))
        last_series_id = cursor.lastrowid
        insert_favorite_query = """
        INSERT INTO user_serien (email, serie) VALUES (%s, %s)
        """
        cursor.execute(insert_favorite_query, (user_email, last_series_id))

        conn.commit()
        return jsonify({'message': 'Series added successfully', 'serie_id': last_series_id}), 201

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()


@series_blueprint.route('/api/series/show', methods=['GET'])
def get_favorite_series():
    from ..app import useremail
    user_email = useremail
    if not user_email:
        return jsonify({'error': 'User not logged in'}), 401
    
    try:
        conn = create_connection()
        cursor = conn.cursor(dictionary=True)
        
        sql = """
        SELECT serien.title, serien.erscheinungsjahr, serien.link, serien.staffeln, genres.genre 
        FROM serien 
        INNER JOIN user_serien ON serien.id = user_serien.serie
        INNER JOIN genres ON serien.genre = genres.id
        WHERE user_serien.email = %s
        """
        cursor.execute(sql, (user_email,))
        series = cursor.fetchall()
        
        return jsonify(series), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500

    finally:
        cursor.close()
        conn.close()