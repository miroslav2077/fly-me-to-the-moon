const PurchaseButton = ({price}) => {
    const formatter = new Intl.NumberFormat('it-IT', {
      style: 'currency',
      currency: 'EUR',
    });
    return (
        <div className="flex flex-col justify-center">
        <button className="bg-green-100 h-full px-4 py-2 rounded-xl">
        🛒
          <h3 className="text-2xl text-center font-bold break-normal">{formatter.format(price)}</h3>
        </button>
      </div>
    )
}


export default PurchaseButton;