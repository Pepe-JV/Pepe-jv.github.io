const { useState, useEffect } = React;
const { motion } = window.Motion || {};

// Configuración de la API de Visual Crossing
const API_KEY = 'YMEDMB6TXN3Z8YT4A5SL98QUE'; // API key de Visual Crossing (cuenta gratuita)
const API_BASE_URL = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline';

// Componente principal de la aplicación
function WeatherApp() {
    const [location, setLocation] = useState('');
    const [weatherData, setWeatherData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [useCurrentLocation, setUseCurrentLocation] = useState(true);

    // Obtener ubicación actual del usuario al cargar
    useEffect(() => {
        if (useCurrentLocation && navigator.geolocation) {
            getCurrentLocationWeather();
        }
    }, []);

    // Función para obtener coordenadas actuales y clima
    const getCurrentLocationWeather = () => {
        setLoading(true);
        setError(null);
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                fetchWeatherData(`${latitude},${longitude}`);
            },
            (error) => {
                setError({
                    title: 'Error de geolocalización',
                    message: 'No se pudo obtener tu ubicación. Por favor, ingresa una ciudad manualmente.'
                });
                setLoading(false);
                setUseCurrentLocation(false);
            }
        );
    };

    // Función para obtener datos del clima
    const fetchWeatherData = async (searchLocation) => {
        setLoading(true);
        setError(null);

        try {
            const url = `${API_BASE_URL}/${encodeURIComponent(searchLocation)}?unitGroup=metric&key=${API_KEY}&contentType=json&include=hours,current`;
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('No se pudo encontrar la ubicación');
            }

            const data = await response.json();
            
            // Procesar datos
            const processedData = {
                location: data.resolvedAddress,
                timezone: data.timezone,
                current: {
                    temp: Math.round(data.currentConditions.temp),
                    feelslike: Math.round(data.currentConditions.feelslike),
                    humidity: data.currentConditions.humidity,
                    windspeed: data.currentConditions.windspeed,
                    precipprob: data.currentConditions.precipprob || 0,
                    conditions: data.currentConditions.conditions,
                    icon: data.currentConditions.icon,
                    datetime: data.currentConditions.datetime
                },
                hourly: getHourlyForecast(data.days),
                daily: data.days.slice(0, 7)
            };

            setWeatherData(processedData);
            setLoading(false);
        } catch (err) {
            setError({
                title: 'Error al cargar el clima',
                message: err.message || 'Por favor, verifica la ubicación e intenta nuevamente.'
            });
            setLoading(false);
        }
    };

    // Obtener pronóstico por horas (24 horas anteriores y futuras)
    const getHourlyForecast = (days) => {
        if (!days || days.length === 0) return [];
        
        const allHours = [];
        
        // Obtener las últimas 12 horas del día anterior
        if (days.length > 1 && days[0].hours) {
            const previousDayHours = days[0].hours.slice(-12);
            allHours.push(...previousDayHours.map(hour => ({
                ...hour,
                date: days[0].datetime
            })));
        }
        
        // Obtener las siguientes 24 horas del día actual
        if (days[1] && days[1].hours) {
            allHours.push(...days[1].hours.map(hour => ({
                ...hour,
                date: days[1].datetime
            })));
        }
        
        return allHours.slice(0, 24);
    };

    // Manejar búsqueda
    const handleSearch = (e) => {
        e.preventDefault();
        if (location.trim()) {
            fetchWeatherData(location);
            setUseCurrentLocation(false);
        }
    };

    // Actualizar pronóstico
    const handleRefresh = () => {
        if (useCurrentLocation) {
            getCurrentLocationWeather();
        } else if (location) {
            fetchWeatherData(location);
        }
    };

    return (
        <div className="app-container">
            <Header />
            <SearchBar
                location={location}
                setLocation={setLocation}
                handleSearch={handleSearch}
                handleRefresh={handleRefresh}
                getCurrentLocation={getCurrentLocationWeather}
                loading={loading}
            />
            
            {error && <ErrorMessage error={error} />}
            
            {loading ? (
                <LoadingState />
            ) : weatherData ? (
                <>
                    <CurrentWeather data={weatherData.current} location={weatherData.location} />
                    <ForecastSection hourly={weatherData.hourly} />
                </>
            ) : !error && (
                <EmptyState />
            )}
        </div>
    );
}

// Componente de encabezado
function Header() {
    return (
        <header className="header">
            <h1>⛅ Weather App</h1>
            <p>Pronóstico del tiempo en tiempo real</p>
        </header>
    );
}

// Componente de búsqueda
function SearchBar({ location, setLocation, handleSearch, handleRefresh, getCurrentLocation, loading }) {
    return (
        <motion.div
            className="search-container"
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
        >
            <form className="search-form" onSubmit={handleSearch}>
                <input
                    type="text"
                    className="search-input"
                    placeholder="Ingresa una ciudad o ubicación..."
                    value={location}
                    onChange={(e) => setLocation(e.target.value)}
                    disabled={loading}
                />
                <button type="submit" className="btn btn-primary" disabled={loading}>
                    🔍 Buscar
                </button>
                <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={handleRefresh}
                    disabled={loading}
                >
                    🔄 Actualizar
                </button>
                <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={getCurrentLocation}
                    disabled={loading}
                >
                    📍 Mi ubicación
                </button>
            </form>
        </motion.div>
    );
}

// Componente de clima actual
function CurrentWeather({ data, location }) {
    const getWeatherIcon = (icon) => {
        const icons = {
            'clear-day': '☀️',
            'clear-night': '🌙',
            'partly-cloudy-day': '⛅',
            'partly-cloudy-night': '☁️',
            'cloudy': '☁️',
            'rain': '🌧️',
            'snow': '❄️',
            'wind': '💨',
            'fog': '🌫️',
            'thunderstorm': '⛈️'
        };
        return icons[icon] || '🌤️';
    };

    return (
        <motion.div
            className="weather-main"
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5 }}
        >
            <div className="current-weather">
                <div className="weather-icon-main">
                    <div className="icon">{getWeatherIcon(data.icon)}</div>
                </div>
                
                <div className="temperature-display">
                    <div className="temperature">{data.temp}°C</div>
                    <div className="condition">{data.conditions}</div>
                    <div className="location">📍 {location}</div>
                </div>
                
                <div className="weather-details">
                    <motion.div
                        className="detail-item"
                        whileHover={{ scale: 1.05 }}
                        transition={{ type: "spring", stiffness: 300 }}
                    >
                        <div className="detail-icon">🌡️</div>
                        <div className="detail-value">{data.feelslike}°C</div>
                        <div className="detail-label">Sensación térmica</div>
                    </motion.div>
                    
                    <motion.div
                        className="detail-item"
                        whileHover={{ scale: 1.05 }}
                        transition={{ type: "spring", stiffness: 300 }}
                    >
                        <div className="detail-icon">💨</div>
                        <div className="detail-value">{data.windspeed} km/h</div>
                        <div className="detail-label">Viento</div>
                    </motion.div>
                    
                    <motion.div
                        className="detail-item"
                        whileHover={{ scale: 1.05 }}
                        transition={{ type: "spring", stiffness: 300 }}
                    >
                        <div className="detail-icon">💧</div>
                        <div className="detail-value">{data.humidity}%</div>
                        <div className="detail-label">Humedad</div>
                    </motion.div>
                    
                    <motion.div
                        className="detail-item"
                        whileHover={{ scale: 1.05 }}
                        transition={{ type: "spring", stiffness: 300 }}
                    >
                        <div className="detail-icon">🌧️</div>
                        <div className="detail-value">{data.precipprob}%</div>
                        <div className="detail-label">Prob. de lluvia</div>
                    </motion.div>
                </div>
            </div>
        </motion.div>
    );
}

// Componente de pronóstico por horas
function ForecastSection({ hourly }) {
    const getWeatherIcon = (icon) => {
        const icons = {
            'clear-day': '☀️',
            'clear-night': '🌙',
            'partly-cloudy-day': '⛅',
            'partly-cloudy-night': '☁️',
            'cloudy': '☁️',
            'rain': '🌧️',
            'snow': '❄️',
            'wind': '💨',
            'fog': '🌫️',
            'thunderstorm': '⛈️'
        };
        return icons[icon] || '🌤️';
    };

    const formatTime = (datetime) => {
        const [hours, minutes] = datetime.split(':');
        return `${hours}:${minutes}`;
    };

    return (
        <div className="forecast-section">
            <h2>Pronóstico por horas (24h)</h2>
            <div className="forecast-container">
                {hourly.map((hour, index) => (
                    <motion.div
                        key={index}
                        className="forecast-card"
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.3, delay: index * 0.05 }}
                        whileHover={{ scale: 1.05, y: -5 }}
                    >
                        <div className="forecast-time">{formatTime(hour.datetime)}</div>
                        <div className="forecast-icon">{getWeatherIcon(hour.icon)}</div>
                        <div className="forecast-temp">{Math.round(hour.temp)}°C</div>
                        <div className="forecast-details">
                            <div>💨 {hour.windspeed} km/h</div>
                            <div>🌧️ {hour.precipprob || 0}%</div>
                        </div>
                    </motion.div>
                ))}
            </div>
        </div>
    );
}

// Componente de carga
function LoadingState() {
    return (
        <motion.div
            className="loading"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
        >
            <div className="loading-spinner"></div>
            <div className="loading-text">Cargando información del clima...</div>
        </motion.div>
    );
}

// Componente de error
function ErrorMessage({ error }) {
    return (
        <motion.div
            className="error"
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0 }}
        >
            <div className="error-icon">⚠️</div>
            <div className="error-content">
                <h3>{error.title}</h3>
                <p>{error.message}</p>
            </div>
        </motion.div>
    );
}

// Componente de estado vacío
function EmptyState() {
    return (
        <motion.div
            className="empty-state"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
        >
            <div className="empty-state-icon">🌍</div>
            <h2>Comienza a explorar el clima</h2>
            <p>Busca una ciudad o usa tu ubicación actual para ver el pronóstico</p>
        </motion.div>
    );
}

// Renderizar la aplicación
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<WeatherApp />);
