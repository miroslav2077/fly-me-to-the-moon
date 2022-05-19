import React from 'react';
import Stop from './Flight/Stop';
import PurchaseButton from './Flight/PurchaseButton';

const Flight = ({ data }) => {
  return (
    <div className="bg-white flex justify-between rounded-xl px-4 py-3">
      <div className="relative">
        <div className="absolute left-1 h-full pt-2 pb-6">
          <div className="bg-orange-100 w-1 h-full"></div>
        </div>
        <ul className="relative font-bold space-y-8 z-10">
          {data.routes.map((route, index) => (
            <React.Fragment key={index}>
              {index === 0 && (
                <Stop data={route.from} />
              )}
              <Stop data={route.to} />
            </React.Fragment>
          ))}
        </ul>
      </div>
    <PurchaseButton price={data.total} />
    </div>
  );
};

export default Flight;
