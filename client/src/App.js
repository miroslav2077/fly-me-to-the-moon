import { useEffect, useState } from "react";
import Select from './components/Airport/Select';
import Flight from './components/Flight';
import "./App.css";

function App() {
  const apiUrl = 'http://localhost:8080';

  const [airports, setAirports] = useState([]);
  const [airportFrom, setAirportFrom] = useState(0);
  const [airportTo, setAirportTo] = useState(1);
  const [flights, setFlights] = useState(null);

  const [isLoading, setIsLoading] = useState(false);

  /* Redux or Context hook would be preferable for more complex structures to avoid prop passing */
  const onAirportFromChange = (e) => {
    setAirportFrom(e.target.value);
  };

  const onAirportToChange = (e) => {
    setAirportTo(e.target.value);
  };
  
  /* Refactor this into Redux or its own helper methods for cleaner code */
  const fetchAirports = async () => {
    setIsLoading(true);
    try {
      const response = await fetch(`${apiUrl}/airports`);
      const json = await response.json();

      setAirports(json);
    } catch(err) {
      setAirports([]);
    }

    setIsLoading(false);
  };

  const fetchFlights = async () => {
    setIsLoading(true);
    try {
      const response = await fetch(
        `${apiUrl}/flights/${airportFrom}/${airportTo}`
      );
      const json = await response.json();
  
      setFlights(json);
    } catch (err) {
      setFlights([]);
    }
    setIsLoading(false);
  };

  const onReverseClickHandler = () => {
    setAirportFrom(prevStateFrom => {
      const to = airportTo;
      setAirportTo(prevStateFrom);
      return to;
    });
  }

  /* Load once on first render */
  useEffect(() => {
    fetchAirports();
  }, []);

  return (
    <div className="App">
      <div className="container mx-auto p-4">
        <div className="flex justify-center align-center">
          <div className="flex flex-col space-y-4 justify-center bg-gradient-to-br from-blue-500 to-cyan-300 px-4 py-3 rounded-2xl md:w-1/2">
            <h1 className="text-white font-bold text-3xl">Ricerca voli 🛫</h1>
            <div className="flex space-x-2">
              <div className="w-full">
                <Select items={airports} value={airportFrom} crossState={airportTo} onChange={onAirportFromChange}/>
                <Select items={airports} value={airportTo} crossState={airportFrom} onChange={onAirportToChange}/>
              </div>
              <button className="rounded-xl" onClick={onReverseClickHandler}>🔃</button>
            </div>
            
            <button
              className="self-center bg-green-400 w-full h-10 hover:bg-green-300 text-white font-bold uppercase rounded-full"
              onClick={fetchFlights}
            >
              Cerca<span className={`inline-block px-2 ${isLoading ? 'animate-spin' : ''}`}>🔎</span>
            </button>

            {flights !== null && flights.map((flight, index) => 
              <Flight key={index} data={flight} />
            )}
            {flights && !flights.length && (
              <p className="font-bold text-white text-center">👩‍✈️: "Mi dispiace, non ho trovato nessun volo..."</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
