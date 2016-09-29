package com.example.heli.searchengine;

import android.content.Intent;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

public class Main2Activity extends AppCompatActivity {
    //TextView tv2;
    ListView listView;
    class Documents {
        String url;
        String title;
        String caption;
        String DoC;
        String thumbnail;

        Documents(String url, String title, String caption, String DoC, String thumbnail) {
            this.url = url;
            this.title = title;
            this.caption = caption;
            this.DoC = DoC;
            this.thumbnail = thumbnail;
        }
    }
    private ArrayList<Documents> documents;
    public static final String REGISTER_URL = "http://192.168.227.1:8082/ita_dse/getAllDocuments1";

    Button btn2;
    EditText search;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main2);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        /*FloatingActionButton fab = (FloatingActionButton) findViewById(R.id.fab);
        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Snackbar.make(view, "Replace with your own action", Snackbar.LENGTH_LONG)
                        .setAction("Action", null).show();
            }
        });*/

        /* iteretive search */
        /*btn2 = (Button) findViewById(R.id.btn1);
        btn2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent i = new Intent(getApplicationContext(), Main3Activity.class);
                i.putExtra("searchstring", search.getText().toString());
                startActivity(i);
                //setContentView(R.layout.activity_main2);
            }
        });*/


        Intent i=getIntent();
        String searchstring=i.getStringExtra("searchstring");
        TextView textView=(TextView)findViewById(R.id.search);
        textView.setText(searchstring);

        StringRequest stringRequest = new StringRequest(Request.Method.POST, REGISTER_URL,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        //Toast.makeText(Main2Activity.this, response, Toast.LENGTH_LONG).show();
                        Toast.makeText(getApplicationContext(), "All Data Successfully fetched", Toast.LENGTH_LONG).show();
                        Log.i("response is:",response);
                        showJSON(response);
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(Main2Activity.this, error.toString(), Toast.LENGTH_LONG).show();
                    }
                }){
        };

        RequestQueue requestQueue = Volley.newRequestQueue(this);
        requestQueue.add(stringRequest);

    }
    private void showJSON(String response){
        System.out.println(response);
        Log.i("response",response);
        if(response!=null){
            documents=new ArrayList<>();
            //System.out.println(result);
            try {
                JSONObject jsonRootObject = new JSONObject(response);

                String status=jsonRootObject.getString("status");

                if("success".equals(status)){
                    JSONArray jsonArray = jsonRootObject.optJSONArray("response");

                    for(int i=0; i < jsonArray.length(); i++){
                        JSONObject jsonObject = jsonArray.getJSONObject(i);
                        // System.out.println("User" + i + ": " + jsonObject.getString("user_name"));
                        //String month = (String) android.text.format.DateFormat.format("MMM", date);
                        String title="", caption="";
                        if(jsonObject.getString("title").length()>35) {
                            title = (jsonObject.getString("title")).substring(0, 35);
                        }else{
                            title = jsonObject.getString("title");
                        }

                        if(jsonObject.getString("title").length()>70) {
                            caption = jsonObject.getString("caption").substring(0, 70);
                        }else{
                            caption = jsonObject.getString("caption");
                        }

                        String DoC = jsonObject.getString("DoC").substring(0, jsonObject.getString("DoC").indexOf(' '));
                        documents.add(new Documents(jsonObject.getString("url"),title,caption,DoC,jsonObject.getString("thumbnail")));

                    }
                    Log.i("document", String.valueOf(documents));
                    CustomAdapter customAdapter=new CustomAdapter(Main2Activity.this, documents);
                    listView=(ListView)findViewById(R.id.listView);
                    listView.setAdapter(customAdapter);
                }else{
                    String message=jsonRootObject.getString("message");
                }

            } catch (JSONException e) {
                e.printStackTrace();
            }
        }else{
        }
    }

}
