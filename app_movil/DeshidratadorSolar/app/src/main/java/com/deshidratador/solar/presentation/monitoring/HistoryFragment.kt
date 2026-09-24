package com.deshidratador.solar.presentation.history

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.deshidratador.solar.R
import com.deshidratador.solar.presentation.adapters.AlertsAdapter
import com.deshidratador.solar.presentation.viewmodel.AlertsViewModel

class HistoryFragment : Fragment() {

    private val viewModel: AlertsViewModel by viewModels()
    private lateinit var adapter: AlertsAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_history, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        recyclerView = view.findViewById(R.id.rvAlertas)
        recyclerView.layoutManager = LinearLayoutManager(requireContext())

        cargarAlertas()

        viewModel.alerts.observe(viewLifecycleOwner) { alertList ->
            alertList?.let {
                adapter = AlertsAdapter(it) {
                    cargarAlertas()
                }
                recyclerView.adapter = adapter
            }
        }
    }

    private fun cargarAlertas() {
        viewModel.loadAlerts(limit = 50)
    }
}