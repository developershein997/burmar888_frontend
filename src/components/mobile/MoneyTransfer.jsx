import React, { useContext ,useState } from 'react'
import WithDraw from './WithDraw';
import '../../assets/css/moneyTransfer.css'
import Deposit from './Deposit';
import Transfer from './Transfer';
import { LanguageContext } from "../../contexts/LanguageContext";

const MoneyTransfer = () => {
   const { content } = useContext(LanguageContext);
    const [selectedTab,setSelectedTab]=useState(1);
    const tabs=[
        {id:1,name: content?.wallet?.deposit || 'Deposit',value:''},
        {id:2,name: content?.wallet?.withdraw ||'Withdraw',value:''},
        // {id:3,name:'လွှဲပြောင်းရန်',value:''},
     ]
  return (
    <div>
        <div className="flex flex-col sm:flex-row gap-1 mb-4 justify-center">
            {tabs.map((tab, index)=>{
                return <div key={index} onClick={()=>setSelectedTab(tab.id)} className={`btn btn-sm py-1 px-3 py-sm-2 mx-a px-sm-5 ${tab.id===selectedTab ? 'bg-warning text-white' : 'bg-light text-white' }  `}>
                    <div className="flex-1 bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-xl px-4 py-2 text-center font-bold shadow">
                    <p className="fw-semibold moneyTransferTabTitle">{tab.name}</p>
                    </div>
                </div>
            })}
        </div>
      <div className="px-sm-5 mx-sm-5 mx-auto my-4 mb-5 pb-5">
        {selectedTab===1 && <Deposit/> }
        {selectedTab===2 && <WithDraw/> }
        {selectedTab===3 && <Transfer/> }
      </div>
    </div>
  )
}

export default MoneyTransfer
