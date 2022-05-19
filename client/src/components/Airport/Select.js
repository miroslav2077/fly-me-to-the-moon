const Select = ({items, value, onChange, crossState = null}) => {

    const onChangeHandler = (e) => {
        onChange(e);
    }

    return (
        <select className="w-full" value={value} onChange={onChangeHandler}>
            {items.map((el) => (
            <option key={el.id} value={el.id} disabled={el.id === Number(crossState)}>
                {el.name}
            </option>
            ))}
        </select>
    )
}

export default Select;