const Stop = ({ data }) => {
    return (
        <li className="flex items-start">
            <span className="w-3 h-3 bg-orange-300 rounded-full inline-block mr-2"></span>
            <div>
            <h3 className="-mt-1">{data.code}</h3>
            <h6 className="text-xs text-slate-400 font-normal">
                {data.name}
            </h6>
            </div>
        </li>
    )
}


export default Stop;