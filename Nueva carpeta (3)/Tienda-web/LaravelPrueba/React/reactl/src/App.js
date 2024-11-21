import React, { useState, useEffect } from 'react';
import logo from './logo.svg';
import './App.css';
import axios from 'axios';

const API_BASE_URL = 'https://covid-api.com/api/';

// Funciones para las solicitudes API
export const fetchRegions = () => axios.get(`${API_BASE_URL}/regions`);
export const fetchCountries = () => axios.get(`${API_BASE_URL}/countries`);
export const fetchProvinces = () => axios.get(`${API_BASE_URL}/provinces`);
export const fetchReports = () => axios.get(`${API_BASE_URL}/reports`);

function App() {
  const [countries, setCountries] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Obtener datos de países al cargar el componente
  useEffect(() => {
    const getCountries = async () => {
      try {
        const response = await fetchCountries();
        setCountries(response.data.data);
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };

    getCountries();
  }, []);

  if (loading) return <p>Cargando datos...</p>;
  if (error) return <p>Error al cargar datos: {error.message}</p>;

  return (
    <div className="App">
      <header className="App-header">
        <img src={logo} className="App-logo" alt="logo" />
        <h1>Datos de COVID-19 por País</h1>
        <ul>
          {countries.map((country) => (
            <li key={country.iso}>
              {country.name} ({country.iso})
            </li>
          ))}
        </ul>
      </header>
    </div>
  );
}

export default App;
